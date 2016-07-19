<?php
/* @var $this common\components\View */
/* @var $model common\models\Event */

use backend\assets\SlugAsset;
use backend\widgets\ImageUploadWidget;
use common\enums\ImageType;
use common\models\Category;
use common\models\Format;
use common\models\Place;
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

/** @var ActiveForm $form */
?>
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
<?= $form->field($model, "image_src")->widget(ImageUploadWidget::classname(), ['type' => ImageType::EVENT, 'src' => $model->getImageSrc()]); ?>

<?= $form->field($model, 'announcement')->textarea(); ?>
<?= $form->field($model, 'description')->widget(Redactor::className()); ?>



    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'format_id')->dropDownList(Format::getList()); ?>
        <?= $form->field($model, 'place_id')->dropDownList(Place::getList()); ?>
        <?= $form->field($model, 'category_ids')->listBox(Category::getList(), ['multiple' => true]); ?>
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
        <?= Html::resetButton('Сбосить', ['class' => 'btn btn-default']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
