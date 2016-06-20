<?php
/* @var $this common\components\View */
/* @var $model common\models\Article */

use backend\widgets\AjaxDropdown\AjaxDropdown;
use common\enums\ArticleType;
use common\models\Article;
use frontend\assets\FileUploadAsset;
use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\redactor\widgets\Redactor;
use yii\web\View;

FileUploadAsset::register($this);
$this->registerJs(<<<JS
    var imageSrc = $("#article-image_src");
    $(".fileinput-button").fileupload({
        url: "/articles/upload",
        dataType: "json",
        done: function (e, data) {
            if (data.result.result) {
                var r = data.result;
                $("#uploaded-image").html("<img src=" + r.src + " />");
                imageSrc.val(r.filename);
                toggleRemoveImage();
            } else {
                alert(data.result.message);
            }
        }
    });

    function toggleRemoveImage()
    {
        $(".remove-image").toggleClass('disabled', imageSrc.val());
        if (imageSrc.val()) {
            $(".image-props").show();
        } else {
            $(".image-props").hide();
        }
    }

    $(".remove-image").click(function(){
        if ($(this).hasClass('disabled')) return;
        $("#uploaded-image").html("");
        $("#article-image_src").val(null);
        toggleRemoveImage();
    })
JS
);
if ($model->isNewRecord) {
    $this->renderHelp('Новые материалы создаются в статусе "Черновик" и не видны пользователям сайта. Сохраните первоначальные данные и дополнительные поля, а также Предпросмотр и другие функции станут доступны');
}
if (!$model->isNewRecord && !$model->published()) {
    $status = $model->isDraft() ? "не опубликован" : "снят с публикации";
    $this->renderAlert('warning', 'Материал '.$status.'. Чтобы опубликовать нажмите кнопку "Опубликовать" внизу страницы.');
}
if (!$model->isNewRecord && !$model->image_src && $model->is_primary) {
    $this->renderAlert('warning', 'Новость не будет показана главной на странице, т.к. к ней не загружена картинка.');
}
if ($model->preview) {
    $this->registerJsFile('@static/js/iframeResizer.min.js', ['position' => View::POS_HEAD]);
}
/** @var \kartik\form\ActiveForm $form */
?>
<? if ($model->preview): ?>
    <iframe src="<?=$model->getViewUrl(true)?>" width="100%" scrolling="no" frameborder="1"></iframe>
    <script type="text/javascript">iFrameResize({})</script>
    <br><br>
<? endif; ?>
<? $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]); ?>
<?= $form->errorSummary($model); ?>
<div class="row-fluid clearfix">
    <div class="col-md-8">
        <?= $form->field($model, 'title')->textInput(); ?>
        <?= $form->field($model, 'sub_title')->textarea(); ?>
        <? if ($model->isArticle()): ?>
        <?= $form->field($model, 'announcement')->textarea(['maxlength' => Article::ANNOUNCEMENT_LENGTH]) ?>
        <? endif; ?>
        <div class="fields-box">
            <h3>Картинка</h3>
            <?= $form->field($model, 'image_src', ['template' => '{input}'])->hiddenInput(); ?>
            <div class="form-group">
                <div class="btn-group btn-group-sm">
                    <div class="btn btn-primary fileinput-button"><span>Загрузить изображение</span>
                        <?=$form->field($model, 'uploadedImage', ['template' => '{input}', 'options' => ['class' => null]])->fileInput(['class' => ''])?>
                    </div>
                    <a class="btn btn-danger remove-image <?=$model->image_src ? '' : 'disabled'; ?>">Удалить</a>
                </div>
            </div>
            <div id="uploaded-image">
                <?php if ($model->image_src): ?>
                    <img src="<?=$model->getImageSrc() ?>" />
                <? endif; ?>
            </div>
            <div class="image-props" style="display: <?=$model->image_src ? 'block' : 'none'; ?>">
                <?= $form->field($model, 'image_source')->textInput(); ?>
                <?= $form->field($model, 'image_url')->textInput(); ?>
                <?= $form->field($model, 'image_description')->textarea(); ?>
            </div>
        </div>
        <?= $form->field($model, 'text')->widget(Redactor::className()) ?>
        <?= $form->field($model, 'meta_title')->textInput() ?>
        <?= $form->field($model, 'meta_description')->textarea() ?>
        <?= $form->field($model, 'meta_keywords')->textarea() ?>
        <?= $form->field($model, 'breadcrumb')->textInput() ?>
    </div>
    <div class="col-md-4">
        <? if (!$model->isNewRecord): ?>
            <div class="form-group">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>Создан:</td><td><strong><?=$model->created_at?></strong></td>
                        <td>Автор:</td><td><strong><?=$model->author->username?></strong></td>
                    </tr>
                    <? if ($model->updated_at != $model->created_at): ?>
                    <tr>
                        <td>Изменен:</td><td><strong><?=$model->updated_at?></strong></td>
                        <td>Автор:</td><td><strong><?=$model->updater->username?></strong></td>
                    </tr>
                    <? endif; ?>
                    <? if ($model->published_at): ?>
                    <tr>
                        <td>Начало пуб.:</td><td><strong><?=$model->published_at?></strong></td>
                    </tr>
                    <? endif; ?>
                </table>
            </div>
        <? endif; ?>
        <? if (!$model->isNewRecord): ?>
            <? if (!$model->isArticle()): ?>
                <?= $form->field($model, 'slug')->textInput() ?>
            <? endif; ?>
            <? if ($model->isArticle()): ?>
                <?= $form->field($model, 'slug')->staticInput() ?>
            <? endif; ?>
        <? endif; ?>
        <?= $form->field($model, 'type')->dropDownList(ArticleType::getList(), ['disabled' => true]); ?>
        <? if ($model->isArticle()): ?>
            <?= $form->field($model, 'in_top')->checkbox(); ?>
            <?= $form->field($model, 'is_primary')->checkbox()->hint("Будет показана в крупном блоке на странице Новости") ?>
        <? endif; ?>
        <?= $form->field($model, 'comments_enabled')->checkbox(['disabled' => $model->isArticle(), 'uncheck' => $model->isArticle() ? '1' : '0']); ?>
        <?= $form->field($model, 'likes_enabled')->checkbox(['disabled' => $model->isArticle(), 'uncheck' => $model->isArticle() ? '1' : '0']); ?>

        <?= $form->field($model, 'bank_ids')->widget(AjaxDropdown::className(), [
            'source' => Url::to(['/banks/filter2']),
            'buttonClass' => 'btn-sm',
            'removeSingleClass' => 'btn-sm',
            'data' => $model->getBanksList()
        ]); ?>
        <?= $form->field($model, 'deposit_category_ids')->widget(AjaxDropdown::className(), [
            'source' => Url::to(['/structure/search2']),
            'buttonClass' => 'btn-sm',
            'removeSingleClass' => 'btn-sm',
            'data' => $model->getDepositCategoriesList()
        ]); ?>
    </div>
</div>
<br>
<div class="form-group">
    <div class="col-md-8 btn-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn '.($model->isNewRecord ? 'btn-success' : 'btn-primary')]) ?>
        <? if (!$model->isNewRecord): ?>
            <?= Html::submitButton('Предпросмотр', ['name'=> 'preview', 'class' => 'btn btn-warning']); ?>
        <? endif; ?>
        <? if ($model->published()): ?>
            <?= Html::a('Перейти', $model->getViewUrl(), ['class' => 'btn btn-info', 'target' => '_blank']); ?>
            <?= Html::submitButton('Снять с публикации', ['name'=> 'unpublish', 'class' => 'btn btn-danger']); ?>
        <? endif; ?>
        <? if (!$model->isNewRecord && !$model->published()): ?>
            <?= Html::submitButton('Опубликовать', ['name'=> 'publish', 'class' => 'btn btn-success']); ?>
        <? endif; ?>
        <?= Html::a('Закрыть', ['/articles/index', 'type' => $model->type], ['class' => 'btn btn-inverse']) ?>
    </div>
</div>


<?php ActiveForm::end(); ?>