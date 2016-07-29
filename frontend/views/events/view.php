<?php

use common\models\Event;
use frontend\components\View;

/**
 * @var $this View
 * @var $model Event
 * @var $flashMessages
 */
$this->title = $model->title;
?>

    <div class="box">
        <div class="col-lg-12">
            <h1 class="intro-text text-center">Cобытие <strong><?=$model->title?></strong></h1>
            <hr class="visible-xs">
            <div class="row" style="margin: 15px -10px;">
                    <div class="col-md-4"><i class="glyphicon glyphicon-time"></i>&nbsp;<strong><?=$model->getTimePeriod()?></strong></div>
                    <div class="col-md-4"><i class="glyphicon glyphicon-map-marker"></i>&nbsp;<strong><?=$model->place->name?>, <?=$model->format->name?></strong></div>
                    <div class="col-md-4">Стоимость:&nbsp;<strong><?=$model->free ? 'Бесплатно' : $model->price_min." р" ?></strong></div>
            </div>
            <?=$model->description?>
        </div>
    </div>