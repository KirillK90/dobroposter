<?php

/**
 * @var $this View
 * @var $content string
 */

use common\enums\ArticleType;
use common\enums\Block;
use common\enums\PagePlace;
use common\enums\SeoParam;
use frontend\assets\AppAsset;
use frontend\components\View;
use frontend\widgets\BestTabsWidget;
use frontend\widgets\Breadcrumbs;
use frontend\widgets\CatalogsWidget;
use frontend\widgets\CategoryBlocksWidget;
use frontend\widgets\FooterWidget;
use frontend\widgets\MainMenuWidget;
use frontend\widgets\RatingInfoBlockWidget;
use frontend\widgets\SearchWidget;
use frontend\widgets\SignupWidget;
use frontend\widgets\SliderWidget;
use frontend\widgets\SocialSubscribeWidget;
use frontend\widgets\TextBlockWidget;
use frontend\widgets\UserMenuWidget;
use yii\helpers\Html;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?= $this->getSeo(SeoParam::META_TITLE, $this->getDefaultTitle()) ?></title>
    <? if ($metaDescription = $this->getSeo(SeoParam::META_DESCRIPTION)): ?>
    <meta content="<?= $metaDescription ?>" name="description">
    <? endif; ?>
    <? if ($metaKeywords = $this->getSeo(SeoParam::META_KEYWORDS)): ?>
        <meta content="<?= $metaKeywords ?>" name="keywords">
    <? endif; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=768">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body class="page">
<?php $this->beginBody() ?>
<?=$this->renderBlock(Block::BODY_BEGIN)?>
<!-- BEGIN BODY -->


<!-- BEGIN HEADER -->

<header class="header">
    <div class="wrapper">
        <div class="header__top">
            <div class="town">
                <a href="<?= $this->getRegionsUrl(); ?>"><i class="town__icon icon-location"></i><?= $this->regionName ?></a>
            </div>
            <?= UserMenuWidget::widget() ?>
            <?= SearchWidget::widget() ?>
        </div>
        <div class="header-bottom">
            <div class="logo"><a href="/"><img src="<?= $this->getLogoUrl() ?>" alt=""></a></div>
            <div class="header-bottom__right">
                <?= $this->getBanner(PagePlace::HEADER) ?>
            </div>
        </div>
        <?= MainMenuWidget::widget(['items' => [
            [
                'title' => 'Поиск вкладов',
                'url' => ['/deposits/search', 'regionSlug' => $this->getRegionSlug()]
            ],
            [
                'title' => 'Виды вкладов',
                'url' => ['/catalogs/index']
            ],
            [
                'title' => 'Банки',
                'url' => ['/banks/index']
            ],
            [
                'title' => 'Аналитика',
                'url' => ['/articles/index', 'type' => ArticleType::ANALYTICS]
            ],
            [
                'title' => 'Новости',
                'url' => ['/articles/index', 'type' => ArticleType::NEWS]
            ],
            [
                'title' => 'Форум',
                'url' => ['/forum']
            ],
        ]]) ?>
    </div>
</header>

<!-- HEADER EOF   -->

<!-- BEGIN CONTENT -->

<div class="content">
    <div class="box-top-filter">
        <div class="wrapper">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?=$this->renderBadge()?>
            <?= $this->renderBlock(Block::CYAN_TOP) ?>
        </div>
    </div>

    <div class="wrapper">

        <?= $this->renderBlock(Block::WHITE_TOP) ?>

        <div class="content-left">
            <?= $content ?>
            <? if ($slider = $this->getMainSlider()): ?>
            <?= SliderWidget::widget(['slider' => $slider]) ?>
            <? endif;?>
            <? if ($this->showBestTabs()): ?>
                <?= BestTabsWidget::widget() ?>
            <? endif;?>
            <? if ($bottomSlider = $this->getBottomSlider()): ?>
                <?= SliderWidget::widget(['slider' => $bottomSlider]) ?>
            <? endif;?>
            <? if ($this->showCatalogs()): ?>
                <?= CatalogsWidget::widget(['bank' => $this->getBank()]) ?>
            <? endif;?>
            <? if (!$this->isStaticPage()): ?>
            <?= TextBlockWidget::widget() ?>
            <? endif; ?>
            <div class="main-img-block banner banner_bottom">
                <?= $this->getBanner(PagePlace::BOTTOM) ?>
            </div>
        </div>

        <div class="content-right">
            <a href="#" class="button-aside">
                <i class="icon-control-left button-aside__icon"></i>
            </a>
            <div class="cont-right-wrap">
                <? if ($banner = $this->getBanner(PagePlace::RIGHT_TOP)): ?>
                <div class="banner<?=$banner->no_border ? '' : ' aside-img-block'?>">
                    <?= $banner; ?>
                </div>
                <? endif; ?>
                <? if ($this->showRatingBlock()): ?>
                    <?= RatingInfoBlockWidget::widget() ?>
                <? endif;?>
                <?= $this->renderBlock(Block::RIGHT_CENTER) ?>
                <?= CategoryBlocksWidget::widget() ?>

                <?=SocialSubscribeWidget::widget() ?>

                <? if ($banner = $this->getBanner(PagePlace::RIGHT_BOTTOM)): ?>
                <div class="banner<?=$banner->no_border ? '' : ' aside-img-block'?>">
<!--                <div class="box-yandex banner">-->
                    <?= $banner; ?>
<!--                </div>-->
                </div>
                <? endif; ?>
            </div>
        </div>

    </div>
    <div id="button-to-top">
        <div class="to-top"></div>
    </div>

</div>

<!-- CONTENT EOF   -->

<!-- BEGIN FOOTER -->

<footer>
    <div class="wrapper">
        <?=FooterWidget::widget() ?>
    </div>
</footer>

<?=SignupWidget::widget() ?>

<?=\Yii::$app->params['trackingCode'] ?>

<!-- FOOTER EOF   -->

<?= $this->render('messageModal'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
