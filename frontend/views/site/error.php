<?php

/* @var $this yii\web\View */
use yii\base\Exception;

/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

?>
<div class="box-head">
    <h1 class="box-head__heading heading heading_lvl_1" ><?=Yii::t('yii', $exception->getMessage()) ?></h1>
</div><!--box-head-->


