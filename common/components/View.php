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
use yii\helpers\Html;

class View extends \yii\web\View
{
    public function renderAlert($type, $text)
    {
        if ($text) {
            echo Html::tag('div', $text, ['class' => "alert-$type alert-block alert"]);
        }
    }

    public function renderHelp($text, $title = null)
    {
        if ($title) {
            $text = "<h4>$title</h4>\n".$text;
        }
        $this->renderAlert('info', $text);
    }

    public function registerGlobals($params)
    {
        $json = json_encode($params);
        $this->registerJs(<<<JS
            if(typeof YII == "undefined"){
                var YII = {};
            }
            (function(){
                "use strict";
                var data = $json;
                for(var x in data) {
                    if(data.hasOwnProperty(x)){
                        YII[x] = data[x];
                    }
                }
            })();

JS
            , View::POS_HEAD);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return Yii::$app->user->getIdentity(false);
    }
}