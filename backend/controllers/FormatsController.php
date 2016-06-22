<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\FormatsFilter;
use common\models\Format;
use Yii;
use yii\base\NotSupportedException;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

/**
 *  Users controller
 */
class FormatsController extends Controller
{

    public function actionIndex()
    {
        $filter = new FormatsFilter();
        $filter->load(Yii::$app->request->get());
        return $this->render('index', compact('filter'));
    }

    public function actionCreate()
    {
        $model = new Format();
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $this->setFlash('success', ACTION_CREATE_SUCCESS);
                $this->redirect(['index']);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->save()) {
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
                $this->redirect(['index']);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            $this->setFlash('success', ACTION_DELETE_SUCCESS);
        } else {
            $this->setFlash('error', ACTION_VALIDATE_ERROR);
        }

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return Format
     * @throws NotSupportedException
     */
    protected function findModel($id)
    {
        if ($model = Format::findOne($id)) {
            return $model;
        }
        throw new NotSupportedException('Категория не найдена.');
    }

}
