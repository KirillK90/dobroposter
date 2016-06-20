<?php
namespace backend\controllers;

use backend\components\Controller;
use common\models\Banner;
use common\models\DepositFilter;
use common\models\Slider;
use Yii;
use yii\base\NotSupportedException;
use yii\bootstrap\ActiveForm;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 *  Sliders controller
 */
class SlidersController extends Controller
{

    public function actionIndex()
    {

        $slider = new Slider();
        $slider->setScenario('search');
        $slider->load(Yii::$app->request->queryParams);
        return $this->render('index', compact('slider'));
    }

    public function actionCreate()
    {
        $model = new Slider();
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $this->setFlash('success', ACTION_CREATE_SUCCESS);
                return $this->redirect(['/sliders/update', 'id' => $model->id]);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }
        $filter = new DepositFilter();

        return $this->render('create', compact('model','filter'));
    }

    public function actionUpdate($id)
    {
        $model = $this->findSlider($id);

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->save()) {
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        $model->deposit_ids = array_keys($model->deposits);

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findSlider($id);

        if ($model->delete()) {
            $this->setFlash('success', ACTION_DELETE_SUCCESS);
        } else {
            $this->setFlash('error', ACTION_VALIDATE_ERROR);
        }

        return $this->redirect(Yii::$app->user->getReturnUrl(['/sliders/index']));
    }

    public function actionUpload($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $id !== null ? $this->findSlider($id) : new Banner();
        $model->setScenario(Banner::SCENARIO_UPLOAD);

        if (!$model->loadFilename()) {
            throw new BadRequestHttpException('File not load.');
        }

        if (!$model->validate()) {
            return ['result' => false, 'errors' =>  $model->getErrors()];
        }
        if (!$model->uploadFilename()) {
            return ['result' => false, 'error' =>  'Ошибка сохранения файла.'];
        }
        if (!$model->isNewRecord && !$model->save()) {
            return ['result' => false, 'error' =>  'Ошибка сохранения записи.'];
        }
        // file is uploaded successfully
        return ['result' => true, 'model' =>  $model->toArray()];
    }

    /**
     * @param $id
     * @return Slider
     * @throws NotSupportedException
     */
    protected function findSlider($id)
    {
        if ($model = Slider::findOne($id)) {
            return $model;
        }
        throw new NotSupportedException('Слайдер не найден.');
    }

}
