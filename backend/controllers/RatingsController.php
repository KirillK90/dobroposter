<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\FactorsForm;
use backend\models\RatingFilter;
use backend\models\WeightsForm;
use common\components\RatingCalculator;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 *  Ratings controller
 */
class RatingsController extends Controller
{

    public function actionIndex()
    {
        $filter = new RatingFilter();
        $filter->load(Yii::$app->request->get());
        return $this->render('index', compact('filter'));
    }

    public function actionUpdate()
    {
        (new RatingCalculator())->run();
        $this->setFlash('success', "Рейтинг успешно пересчитан");
        $this->redirect(['/ratings/index']);
    }

    public function actionWeights()
    {
        $model = new WeightsForm();
        $model->loadWeights();
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
        return $this->render('weights', compact('model'));
    }

    public function actionFactors()
    {
        $model = new FactorsForm();
        $model->loadValues();
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
        return $this->render('factors', compact('model'));
    }
}
