<?php

/* @var $this View */
use frontend\components\View;
use frontend\models\ChangePasswordForm;
use frontend\widgets\UiForm;

/* @var $model ChangePasswordForm */
$this->registerJs(<<<JS
    $('#passwordForm').on('ajaxComplete', function(e, xhr, status) {
    var data = $(this).data('yiiActiveForm');
    if (xhr.responseJSON.success && status == 'success') {
      var result =  $('.userbox-results', $(this));
      result.text('Изменение прошло успешно');
      setTimeout(function(){
         result.text('');
      }, 5000)
    }
  }).on('beforeSubmit', function(e) {
    return false;
  });
JS
);
?>
<? $form = UiForm::begin([
    'fieldConfig' => ['options' => ['class' => 'box-field middle']],
    'options' => ['id' => 'passwordForm', 'class' => 'userbox userbox_private']]) ?>
    <h2 class="heading heading_lvl_2 user__heading">Смена пароля</h2>
    <?=$form->field($model, 'password')->passwordInput()/*->hint('<a href="#pass" class="forget-pass">Напомнить <br> пароль</a>')*/?>
    <?=$form->field($model, 'newPassword')->passwordInput()?>
    <?=$form->field($model, 'repeatPassword')->passwordInput()?>
    <div class="box-field userbox__save">
        <label class="box-field__lbl"></label>
        <button class="button button_size_m button_width_auto" type="submit">Сохранить</button>
        <span class="userbox-results userbox-results_success"></span>
    </div>
<? UiForm::end() ?>