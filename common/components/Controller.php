<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/21/15
 * Time: 4:02 PM
 */

namespace common\components;


use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\Response;
use yii\widgets\ActiveForm;

class Controller extends \yii\web\Controller
{
    /**
     * Render JSON data for ajax
     * If data is_bool then render json where key='result' and value=data
     * @param mixed $data
     * @param string $message
     */
    protected function renderJSON($data, $message = '')
    {
        if (is_bool($data))
        {
            $data = array('success' => $data, 'message' => $message);
        }
        header("Content-type: application/json; charset=utf-8");
        echo Json::encode($data);
        Yii::$app->end();
    }

    protected function setFlash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type, $message);
    }

    protected function performAjaxValidation(Model $model)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return Yii::$app->user->identity;
    }
}