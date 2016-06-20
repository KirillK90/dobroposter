<?php

/* @var $this View */
/* @var $profileForm ProfileForm */
/* @var $changePasswordForm ChangePasswordForm */
/* @var $message string */
/* @var $exception Exception */

use common\enums\SeoParam;
use frontend\assets\FileUploadAsset;
use frontend\assets\FilterAsset;
use frontend\components\View;
use frontend\models\ChangePasswordForm;
use frontend\models\ProfileForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

FilterAsset::register($this);
FileUploadAsset::register($this);

if ($breadcrumb = $this->getSeo(SeoParam::BREADCRUMB)) {
    $this->params['breadcrumbs'][] = $breadcrumb;
}
$uploadUrl = Url::to(['/site/upload-photo', 'user_id' => $profileForm->id]);
$this->registerJs(<<<JS
    $(".fileinput-button").fileupload({
        url: "{$uploadUrl}",
        dataType: "json",
        done: function (e, data) {
            if (data.result.result) {
                var r = data.result;
                $(".user-photo__img").attr("src", r.src);
            } else {
                console.log(data.result.message);
                alert("Ошибка");
            }
        }
    });
JS
);
?>
<? if ($h1 = $this->getSeo(SeoParam::H1)): ?>
    <div class="box-head">
        <h1 class="box-head__heading heading heading_lvl_1" ><?=$h1 ?></h1>
        <? if ($subTitle = $this->getSeo(SeoParam::SUB_TITLE)): ?>
            <p class="box-head__desc"><?=$subTitle?></p>
        <? endif; ?>
    </div><!--box-head-->
<? endif; ?>
<section class="user">
    <div class="user__aside">
        <div class="user-photo">
            <div class="user-photo__cont">
                <img class="user-photo__img" src="<?=$profileForm->imageUrl()  ?>">
            </div>
            <? $form = ActiveForm::begin() ?>
            <a class="user-photo__change link fileinput-button">Изменить фото<?=Html::activeFileInput($profileForm, 'uploadedImage')?></a>
            <? ActiveForm::end() ?>
            <?php //a class="user-photo__change link">Изменить фото</a ?>
        </div><!--user-photo-->
    </div>
    <div class="user__content">
        <div class="user__container">
            <?=$this->render('_profileForm', ['model' => $profileForm])?>
            <? if (!$profileForm->oauth): ?>
            <?=$this->render('_passwordForm', ['model' => $changePasswordForm])?>
            <? endif; ?>
        </div>
    </div>
</section>


