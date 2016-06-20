<?php

namespace backend\controllers;

use backend\components\Controller;
use common\models\Deposit;
use common\models\DepositData;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * DepositsController implements the CRUD actions for Deposit model.
 */
class DepositsController extends Controller
{

    /**
     * Filters list of banks
     * @param $q
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionFilter($q=null)
    {
        $ids = Yii::$app->request->post('ids');
        Yii::$app->response->format=Response::FORMAT_JSON;
        $query = Deposit::find()->select(['product_name as name', 'deposit.id', 'bank.name as bank_name'])
            ->innerJoinWith(['bank' => function (Query $query) {
                $query->select('bank.name');
            }], false);
        if ($q) {
            $query->andWhere(['or', ['ilike', 'product_name', $q], ['ilike', 'bank.name', $q]]);
        }
        if ($ids) {
            $query->andWhere(['deposit.id' => $ids]);
        }
        $rawData = $query->orderBy('product_name')->limit(20)->asArray()->all();
        $data = [];
        foreach ($rawData as $row) {
            $data[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'group' => $row['bank_name'],
                'selected' => (boolean)$ids
            ];
        }
        return $data;
    }

    /**
     * Updates an existing Deposit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $deposit = $this->findModel($id);
        if (!$model = $deposit->depositData) {
            $model = new DepositData();
            $model->id = $deposit->id;
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
            } else {
                $this->setFlash('danger', ACTION_VALIDATE_ERROR);
            }
        } else {
            $model->loadCategories($deposit);
        }

        return $this->render('update', [
            'model' => $model,
            'deposit' => $deposit,
        ]);

    }

    /**
     * Finds the Deposit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Deposit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Deposit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
