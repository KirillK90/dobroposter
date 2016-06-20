<?php
/* @var $this common\components\View */
/* @var $model common\models\User */

use common\enums\Gender;
use common\enums\UserRole;
use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\jui\DatePicker;

/** @var \kartik\form\ActiveForm $form */
$form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL,
        'fullSpan' => 8,
        'formConfig' => [
            'labelSpan' => 2,
        ],
    ]
); ?>
<? if (!$model->isNewRecord): ?>
<?= $form->field($model, 'id')->staticInput(); ?>
<?= $form->field($model, 'created_at')->staticInput(); ?>
<?= $form->field($model, 'updated_at')->staticInput(); ?>
<?= $form->field($model, 'lastVisit')->staticInput(); ?>
<? endif ?>
<?= $form->field($model, 'email')->textInput(); ?>
<?= $form->field($model, 'username')->textInput(); ?>
<?= $form->field($model, 'role')->dropDownList(UserRole::getList(), ['prompt' => 'Не выбрано']); ?>
<?= $form->field($model, 'gender')->dropDownList(Gender::getList(), ['prompt' => 'Не выбрано']); ?>
<?= $form->field($model, 'birthday')->widget(DatePicker::className()); ?>

<?= $form->field($model, 'newPassword', ['labelOptions' => ['label' => 'Пароль']])->passwordInput(['style' => 'width: 142px']); ?>



<div class="form-group">
    <div class="col-sm-offset-2 btn-group btn-group-sm">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn '.($model->isNewRecord ? 'btn-success' : 'btn-primary')]) ?>
        <?= Html::resetButton('Сбосить', ['class' => 'btn btn-default']) ?>
    </div>
</div>


<?php ActiveForm::end(); ?>