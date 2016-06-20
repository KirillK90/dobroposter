<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\BannersFilter;
use common\enums\BannerType;
use common\models\Banner;
use Yii;
use yii\base\NotSupportedException;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 *  Banners controller
 */
class BannersController extends Controller
{

    public function actionIndex()
    {
        $filter = new BannersFilter();
        $dataProvider = $filter->search(Yii::$app->request->get());
        return $this->render('index', compact('dataProvider', 'filter'));
    }

    public function actionCreate()
    {
        $model = new Banner();
        $model->type = BannerType::IMG;
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $this->setFlash('success', ACTION_CREATE_SUCCESS);
                return $this->redirect(Yii::$app->user->getReturnUrl(['/banners/index']));
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findBanner($id);

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
                return $this->redirect(Yii::$app->user->getReturnUrl(['/banners/index']));
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findBanner($id);
        if ($model->delete()) {
            $this->setFlash('success', ACTION_DELETE_SUCCESS);
        } else {
            $this->setFlash('error', ACTION_VALIDATE_ERROR);
        }

        return $this->redirect(Yii::$app->user->getReturnUrl(['/banners/index']));
    }

    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $banner = new Banner() ;
        $banner->setScenario($banner::SCENARIO_UPLOAD);
        $banner->load(Yii::$app->request->post());
        $banner->uploadedImage = UploadedFile::getInstance($banner, 'uploadedImage');

        if ($banner->upload()) {
            return ['result' => true, 'src' => $banner->getFilenameUrl(), 'filename' => $banner->filename];
        }
        return ['result' => false, 'message' => reset($banner->getErrors())];
    }

    /**
     * @param $id
     * @return Banner
     * @throws NotSupportedException
     */
    protected function findBanner($id)
    {
        if ($model = Banner::findOne($id)) {
            return $model;
        }
        throw new NotSupportedException('Банер не найден.');
    }

}
