<?php

/* @var $this View */
/* @var $model SignupForm */

use common\enums\SeoParam;
use frontend\assets\FilterAsset;
use frontend\components\View;
use frontend\models\SignupForm;
use frontend\widgets\SocialAuthWidget;
use frontend\widgets\UiForm;
use yii\helpers\Url;

FilterAsset::register($this);

if ($breadcrumb = $this->getSeo(SeoParam::BREADCRUMB, 'Регистрация')) {
    $this->params['breadcrumbs'][] = $breadcrumb;
}

?>
<section class="signup">
    <div class="signup__content">
    <div class="signup__container">
        <? $form = UiForm::begin(['options' => ['id' => "loginform3", 'class' => 'userbox']]) ?>
        <? if ($h1 = $this->getSeo(SeoParam::H1, 'Регистрация')): ?>
            <div class="box-head">
                <h1 style="text-align: center" class="box-head__heading heading heading_lvl_1" ><?=$h1 ?></h1>
                <? if ($subTitle = $this->getSeo(SeoParam::SUB_TITLE)): ?>
                    <p class="box-head__desc"><?=$subTitle?></p>
                <? endif; ?>
            </div><!--box-head-->
        <? endif; ?>
        <?=$form->field($model, 'username') ?>
        <?=$form->field($model, 'email') ?>
        <?=$form->field($model, 'password')->passwordInput() ?>
        <?=$form->field($model, 'accept')->styledCheckbox() ?>
        <div class="userbox__footer">
            <div class="box-field userbox__save">
                <label class="box-field__lbl"></label>
                <button class="button button_size_m button_width_auto" type="submit">Зарегистрироваться</button>
                <span class="userbox-results userbox-results_success"><!--Изменение прошло успешно--></span>
                <?=SocialAuthWidget::widget([
                    'title' => 'Зарегистрироваться с помощью',
                    'action' => Url::to(['site/eauth'])
                ]);?>
            </div>
        </div>
    </div>
    </div>

    <? UiForm::end() ?>
</section>


