<?php
/* @var $this common\components\View */
/* @var $model common\models\Event */

use common\models\Format;
use common\models\Place;
use kartik\datetime\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\redactor\widgets\Redactor;

/** @var ActiveForm $form */
?>
<div class="row">
    <div class="col-md-8">
<? $form = ActiveForm::begin(); ?>
<? if (!$model->isNewRecord): ?>
<?= $form->field($model, 'id')->staticControl(); ?>
<?= $form->field($model, 'created_at')->staticControl(); ?>
<? endif ?>
<?= $form->field($model, 'name')->textInput(); ?>
<?= $form->field($model, 'format_id')->dropDownList(Format::getList()); ?>
<?= $form->field($model, 'place_id')->dropDownList(Place::getList()); ?>
<?//= $form->field($model, 'image_src')->widget(Place::getList()); ?>
<?= $form->field($model, 'announcement')->textarea(); ?>
<?= $form->field($model, 'description')->widget(Redactor::className()); ?>




<?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'start_time')->widget(DateTimePicker::className(), [
            'pluginOptions' => [
                'autoclose' => true,
                'value' => date('Y-m-d H:i').':00',
                'format' => 'yyyy-mm-dd hh:ii'
            ]
        ]); ?>
        <?= $form->field($model, 'end_time')->widget(DateTimePicker::className(), [
            'pluginOptions' => [
                'autoclose' => true,
                'value' => date('Y-m-d H:i').':00',
                'format' => 'yyyy-mm-dd hh:ii'
            ]
        ]); ?>
        <?= $form->field($model, 'free')->checkbox(); ?>
    </div>
</div>
<div class="form-group">
    <div class="btn-group btn-group-sm">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn '.($model->isNewRecord ? 'btn-success' : 'btn-primary')]) ?>
        <?= Html::resetButton('Сбосить', ['class' => 'btn btn-default']) ?>
    </div>
</div>
