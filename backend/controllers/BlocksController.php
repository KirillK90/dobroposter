<?php
namespace backend\controllers;

use backend\components\Controller;
use common\models\CategoryBlock;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Collections controller
 */
class BlocksController extends Controller
{

    public function actionIndex()
    {
        $blocks = CategoryBlock::find()->orderBy('order asc')->all();

        return $this->render('index', compact('blocks'));
    }

    /**
     * Updates an existing Bank model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionSave($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            $block = $this->findModel($id);
        } else {
            $block = new CategoryBlock();
        }

        if ($block->load(Yii::$app->request->post()) && $block->save()) {
            return ['result' => true, 'id' => $block->id];
        }

        return  ['result' => false, 'message' => reset($block->getFirstErrors())];

    }

    /**
     * Action for ajax js widget fileUpload
     * @return array
     */
    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $block = new CategoryBlock();
        $block->uploadedImage = UploadedFile::getInstance($block, 'uploadedImage');;
        if (!$block->uploadedImage) {
            return ['result' => false, 'errors' => ['Файл не отправлен']];
        }
        if ($filename = $block->upload()) {
            // file is uploaded successfully
            return ['result' => true, 'baseUrl' => $block->uploadUrl, 'filename' => $filename];
        }
        return ['result' => false, 'message' => $block->getFirstError('uploadedImage')];
    }

    public function actionSaveOrder()
    {
        $ids = Yii::$app->request->post('order');
        $ids = array_filter($ids);
        Yii::$app->response->format = Response::FORMAT_JSON;

        $cnt = CategoryBlock::getDb()->transaction(function() use ($ids) {
            $cnt = 0;
            foreach($ids as $order => $id) {
                $cnt += CategoryBlock::updateAll(['order' => $order], ['id' => $id]);
            }
            return $cnt;
        });

        return ['result' => $cnt];
    }

    public function actionRemove($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $block = $this->findModel($id);
        return ['result' => $block->delete()];
    }

    /**
     * Finds the Bank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CategoryBlock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CategoryBlock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
