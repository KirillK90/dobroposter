<?php 

namespace common\components\helpers;

use Yii;
use yii\helpers\Html;

/**
 * Иконки бутстрапа
 */
class Icon
{
    public static function i($name)
    {
        return Html::tag('i', '', ['class' => "glyphicon glyphicon-$name"]);
    }
}