<?php
namespace backend\controllers;

use backend\components\Controller;
use common\models\MainIcon;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Icons controller
 */
class IconsController extends Controller
{

    public function actionIndex()
    {
        $icons = MainIcon::find()->orderBy('order asc')->all();

        return $this->render('index', compact('icons'));
    }

    /**
     * Updates an existing Bank model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionSave($id)
    {
        $icon = $this->findModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($icon->load(Yii::$app->request->post()) && $icon->save()) {
            return ['result' => true];
        }

        return ActiveForm::validate($icon);

    }

    /**
     * Action for ajax js widget fileUpload
     * @param $id
     * @return array
     */
    public function actionUpload($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $icon = $this->findModel($id);
        $icon->setScenario($icon::SCENARIO_UPLOAD);

        if ($icon->load(Yii::$app->request->post()) && $icon->upload()) {
            // file is uploaded successfully
            return array_merge(['result' => true, 'baseUrl' => $icon->uploadUrl], $icon->getAttributes(['filename', 'filename_hover']));
        }
        return ['result' => false, 'errors' => reset($icon->getErrors())];
    }

    public function actionSaveOrder()
    {
        $ids = Yii::$app->request->post('icons');

        Yii::$app->response->format = Response::FORMAT_JSON;

        $cnt = MainIcon::getDb()->transaction(function() use ($ids) {
            $cnt = 0;
            foreach($ids as $order => $id) {
                $cnt += MainIcon::updateAll(['order' => $order], ['id' => $id]);
            }
            return $cnt;
        });

        return ['result' => $cnt];
    }

    /**
     * Finds the Bank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MainIcon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MainIcon::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
