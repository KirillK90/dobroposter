<?php
/* @var $this common\components\View */
/* @var $model common\models\Event */

use backend\assets\SlugAsset;
use backend\widgets\ImageUploadWidget;
use common\components\View;
use common\enums\ImageType;
use common\models\Category;
use common\models\Format;
use common\models\Place;
use dosamigos\multiselect\MultiSelect;
use kartik\datetime\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\redactor\widgets\Redactor;

SlugAsset::register($this);
$this->registerJs(<<<JS
    $("#event-free").change(function(){
            $(".field-event-price_min").toggle(!$(this).prop('checked'));
        }).trigger('change');

    $('#event-slug').focus(function(){
        var name = $("#event-name").val();
        if (!$(this).val() && name) {
            $(this).val(getSlug(name));
        }
    });
JS
);

if ($model->isNewRecord) {
    $this->renderHelp('Новые cобытия создаются в статусе "Черновик" и не видны пользователям сайта. Сохраните первоначальные данные и дополнительные поля, а также Предпросмотр и другие функции станут доступны');
}
if (!$model->isNewRecord && !$model->published()) {
    $status = $model->isDraft() ? "не опубликован" : "снят с публикации";
    $this->renderAlert('warning', 'Материал '.$status.'. Чтобы опубликовать нажмите кнопку "Опубликовать" внизу страницы.');
}

if ($model->preview) {
    $this->registerJsFile('@static/js/iframeResizer.min.js', ['position' => View::POS_HEAD]);
}

/** @var ActiveForm $form */
?>
<? if ($model->preview): ?>
    <iframe src="<?=$model->getViewUrl(true)?>" width="100%" scrolling="no" frameborder="1"></iframe>
    <script type="text/javascript">iFrameResize({checkOrigin:false, heightCalculationMethod: 'bodyScroll'})</script>
    <br><br>
<? endif; ?>
<? $form = ActiveForm::begin(); ?>
<div class="row">

    <div class="col-md-8">
        <?= $form->errorSummary($model)?>
<? if (!$model->isNewRecord): ?>
<?= $form->field($model, 'id')->staticControl(); ?>
<?= $form->field($model, 'created_at')->staticControl(); ?>
<? endif ?>
<?= $form->field($model, 'title')->textInput(); ?>
<?= $form->field($model, 'slug', [
            'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Yii::getAlias('@site').'/events/</span>{input}</div>'
        ])->textInput(); ?>
<?= $form->field($model, "image_src")->widget(ImageUploadWidget::className(), ['type' => ImageType::EVENT, 'src' => $model->getImageSrc()]); ?>

<?= $form->field($model, 'announcement')->textarea(); ?>
<?= $form->field($model, 'description')->widget(Redactor::className()); ?>



    </div>
    <div class="col-md-4">
        <? if (!$model->isNewRecord): ?>
            <div class="form-group">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>Создан:</td><td><strong><?=$model->created_at?></strong></td>
                        <td>Автор:</td><td><strong><?=$model->author->username?></strong></td>
                    </tr>
                    <? if ($model->updated_at && $model->updated_at != $model->created_at): ?>
                        <tr>
                            <td>Изменен:</td><td><strong><?=$model->updated_at ?: " не изменен " ?></strong></td>
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
        <?= $form->field($model, 'format_id')->dropDownList(Format::getList(), ['prompt' => 'Не выбрано']); ?>
        <?= $form->field($model, 'place_id')->dropDownList(Place::getList(), ['prompt' => 'Не выбрано']); ?>
<!--        --><?//= $form->field($model, 'category_ids')->listBox(Category::getList(), ['multiple' => true]); ?>
        <?= $form->field($model, 'category_ids')->widget(MultiSelect::className(), [
            'data' => Category::getList(),
            "options" => ['multiple'=>"multiple"], // for the actual multiselect
            'clientOptions' => [
                'buttonContainer' => '<div class="input-group btn-group btn-group-sm"></div>',
                'nonSelectedText' => 'Не выбрано'
            ],
        ]); ?>
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
        <?= $form->field($model, 'in_top')->checkbox(); ?>
        <?= $form->field($model, 'free')->checkbox(); ?>
        <?= $form->field($model, 'price_min',  [
            'inputTemplate' => '<div class="input-group">{input}<span class="input-group-addon">рублей</span></div>'
        ])->textInput(); ?>
    </div>
</div>
<div class="form-group">
    <div class="btn-group btn-group-sm">
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
    </div>
</div>
<?php ActiveForm::end(); ?>
