<?php

use common\enums\SeoParam;
use frontend\components\View;
use frontend\models\SearchForm;
use frontend\widgets\SearchResultsWidget;
use frontend\widgets\UiForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this View */
/* @var $model SearchForm */
/* @var $exception Exception */

if ($breadcrumb = $this->getSeo(SeoParam::BREADCRUMB)) {
    $this->params['breadcrumbs'][] = $breadcrumb;
}
?>
<div id="pagestart">
    <? if ($h1 = $this->getSeo(SeoParam::H1)): ?>
        <div class="box-head">
            <h1 class="box-head__heading heading heading_lvl_1" ><?=$h1 ?></h1>
            <? if ($subTitle = $this->getSeo(SeoParam::SUB_TITLE)): ?>
                <p class="box-head__desc"><?=$subTitle?></p>
            <? endif; ?>
        </div><!--box-head-->
    <? endif; ?>
</div>

<? $form = UiForm::begin(['action' => Url::to(['site/search']), 'method' => 'get', 'options' => ['class' => 'searchform']]) ?>
    <?=Html::textInput('q', $model->q, ['class' => 'input input_type_text input_width_available input_size_s searchform__field'])?>
    <div class="searchform__send">
        <button type="submit" class="button button_size_small ">Искать</button>
    </div>
 <? UiForm::end() ?>
<?= SearchResultsWidget::widget(['dataProvider' => $model->search()])?>

