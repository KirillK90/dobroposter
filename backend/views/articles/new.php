<?php

use backend\models\NewPageForm;
use common\enums\ArticleType;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this common\components\View */
/* @var $model NewPageForm */

$this->title = 'Добавление материала';
$this->params['breadcrumbs'][] = $this->title;

?>

<div style="border: 1px solid gray;
    border-radius: 10px;
    margin: auto;
    padding: 10px 20px;
    width: 400px;">
    <? /** @var \kartik\form\ActiveForm $form */
    $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_VERTICAL,
        'fullSpan' => 8,
        'formConfig' => [
            'labelSpan' => 2,
        ],
        'fieldConfig' => [
            'showLabels' => false
        ]
    ]); ?>
    <h3>Выберите тип материала</h3>
    <?= $form->field($model, 'type')->radioList(ArticleType::getList()); ?>
    <div class="form-group">
        <div class="btn-group btn-group-sm">
            <?= Html::submitButton('Продолжить', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
    <? ActiveForm::end() ?>
</div>
