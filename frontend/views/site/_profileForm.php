<?php

/* @var $this View */
use common\enums\Gender;
use frontend\components\View;
use frontend\models\ProfileForm;
use frontend\widgets\UiForm;

/* @var $model ProfileForm */
/* @var $form UiForm */

$this->registerJs(<<<JS
    $('#profileForm').on('ajaxComplete', function(e, xhr, status) {
    var data = $(this).data('yiiActiveForm');
    if (xhr.responseJSON.success && status == 'success') {
      $(this).yiiActiveForm('resetForm');
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
    'options' => ['id' => 'profileForm', 'class' => 'userbox']]) ?>
    <h2 class="heading heading_lvl_2 user__heading">Личные данные</h2>
    <div class="userbox__info">
        <?=$form->field($model, 'username')?>
        <?=$form->field($model, 'birthday')->date()?>
        <?=$form->field($model, 'gender')->dropDownList(Gender::getList(), ['prompt' => 'Выберите', 'class' => 'styled'])?>
        <?=$form->field($model, 'email')?>
        <?=$form->field($model, 'is_subscribed')->styledCheckbox()?>
        <? if ($model->oauth): ?>
            <?=$form->field($model, 'service')->infoField(['class' => 'box-field__static'])?>
        <? endif; ?>


        <div class="userbox__footer">
            <div class="box-field userbox__save">
                <label class="box-field__lbl"></label>
                <button class="button button_size_m button_width_auto" type="submit">Сохранить</button>
                <span class="userbox-results userbox-results_success"><!--Изменение прошло успешно--></span>
            </div>
        </div>
    </div><!--info-->
<? UiForm::end() ?>

