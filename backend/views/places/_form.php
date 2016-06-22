<?php
/* @var $this common\components\View */
/* @var $model common\models\Place */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var ActiveForm $form */
?>
<div class="row">
    <div class="col-md-5">
<? $form = ActiveForm::begin(); ?>
<? if (!$model->isNewRecord): ?>
<?= $form->field($model, 'id')->staticControl(); ?>
<?= $form->field($model, 'created_at')->staticControl(); ?>
<? endif ?>
<?= $form->field($model, 'name')->textInput(); ?>

<div class="form-group">
    <div class="btn-group btn-group-sm">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn '.($model->isNewRecord ? 'btn-success' : 'btn-primary')]) ?>
        <?= Html::resetButton('Сбосить', ['class' => 'btn btn-default']) ?>
    </div>
</div>


<?php ActiveForm::end(); ?>
    </div>
</div>
