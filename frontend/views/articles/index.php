<?php
/**
 * @var $this View
 * @var $topArticles Article[]
 * @var $dataProvider ActiveDataProvider
 * @var $type string
 */

use common\enums\ArticleType;
use common\enums\Block;
use common\enums\SeoParam;
use common\models\Article;
use common\models\Deposit;
use frontend\components\View;
use frontend\widgets\ArticlesListWidget;
use frontend\widgets\DepositsBlockWidget;
use frontend\widgets\TopArticlesWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

if ($breadcrumb = $this->getSeo(SeoParam::BREADCRUMB)) {
    $this->params['breadcrumbs'][] = $this->getSeo(SeoParam::BREADCRUMB);
}

//Не показывать блок "Лучшее в разделах"
$this->params['showBestTabs'] = false;

?>
<? $this->beginBlock(Block::RIGHT_CENTER); ?>
<?=DepositsBlockWidget::widget([
    'header' => 'Вклады с высшим рейтингом',
    'deposits' => Deposit::getBest(),
]) ?>
<br>
<a class="all-categories" href="<?=Url::to(['deposits/search'])?>">Все Предложения</a>
<? $this->endBlock(); ?>
<div id="invest" class="articles">
    <div class="box-head box-head_without-padding">
        <? if($h1 = $this->getSeo(SeoParam::H1, ArticleType::getName($type))): ?>
            <h1 class="box-head__heading heading heading_lvl_1"><?=$h1?></h1>
        <? endif; ?>
        <? if($subTitle = $this->getSeo(SeoParam::SUB_TITLE)): ?>
            <div style="font-style: italic" class="box-head__desc"><?=$subTitle?></div>
        <? endif; ?>
    </div>
    <? if ($type == ArticleType::ANALYTICS): ?>
        <?=$this->render('_analytics')?>
        <?=$this->render('_monitoring')?>
    <? endif; ?>
    <? if ($topArticles): ?>
    <?=TopArticlesWidget::widget(['articles' => $topArticles, 'withPrimary' => $type == ArticleType::NEWS])?>
    <? endif; ?>
    <? if ($dataProvider->getModels()): ?>
    <?=ArticlesListWidget::widget(['dataProvider' => $dataProvider])?>
    <? endif; ?>
</div>
