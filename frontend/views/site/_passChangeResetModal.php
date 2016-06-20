<?php

use frontend\models\PasswordResetChangeForm;
use frontend\widgets\UiForm;

$this->registerJs(<<<JS
    $.fancybox({content: $('#newpass'),
      padding: 0
    });
JS
);

/** @var PasswordResetChangeForm $passwordForm */
?>

<div class="window-open result-win" id="newpass">
    <div class="box-form">
        <? $form = UiForm::begin([
            'fieldConfig' => ['options' => ['class' => 'box-field middle']],
            'options' => ['id' => 'passwordForm', 'class' => 'userbox userbox_private']]) ?>
        <div class="heading heading_lvl_2 user__heading">Смена пароля</div>
        <?=$form->field($passwordForm, 'newPassword')->passwordInput() ?>
        <?=$form->field($passwordForm, 'repeatPassword')->passwordInput() ?>
        <div class="box-field userbox__save">
            <label class="box-field__lbl"></label>
            <button class="button button_size_m button_width_auto" type="submit">Сохранить</button>
            <span class="userbox-results userbox-results_success"></span>
        </div>
        <? UiForm::end() ?>
    </div>
</div>