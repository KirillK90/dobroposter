<?php
/* @var $this common\components\View */
/* @var $model common\models\User */

use common\enums\Gender;
use common\enums\UserRole;
use kartik\datetime\DateTimePicker;
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
<?= $form->field($model, 'updated_at')->staticControl(); ?>
<?= $form->field($model, 'lastVisit')->staticControl(); ?>
<? endif ?>
<?= $form->field($model, 'email')->textInput(); ?>
<?= $form->field($model, 'newPassword', ['labelOptions' => ['label' => 'Пароль']])->passwordInput(['style' => 'width: 142px']); ?>
<?= $form->field($model, 'username')->textInput(); ?>
<?= $form->field($model, 'role')->dropDownList(UserRole::getList(), ['prompt' => 'Не выбрано']); ?>
<?= $form->field($model, 'gender')->dropDownList(Gender::getList(), ['prompt' => 'Не выбрано']); ?>
<?= $form->field($model, 'birthday')->widget(DateTimePicker::className(), [
    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
    'pluginOptions' => [
        'autoclose' => true,
        'minView' => 'month',
        'value' => date('Y-m-d'),
        'format' => 'yyyy-mm-dd'
    ]
]); ?>


<div class="form-group">
    <div class="btn-group btn-group-sm">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn '.($model->isNewRecord ? 'btn-success' : 'btn-primary')]) ?>
        <?= Html::resetButton('Сбосить', ['class' => 'btn btn-default']) ?>
    </div>
</div>


<?php ActiveForm::end(); ?>
    </div>
</div>
