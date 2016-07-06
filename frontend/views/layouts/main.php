<?php

/**
 * @var $this View
 * @var $content string
 */

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
use yii\widgets\Menu;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?=$this->title ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=768">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body class="page">
<?php $this->beginBody() ?>
<!-- BEGIN BODY -->


<!-- BEGIN HEADER -->

<div id="container">

    <!-- header -->
    <header id="header">
        <a class="site-header-logo"><?=Yii::$app->name?></a>
    </header>
    <nav id="menu" class="clearfix">
        <?=Menu::widget([
            'items' => [
                ['label' => 'Афиша', 'url' => ['events/index']],
                ['label' => 'О проекте', 'url' => ['site/about']],
                ['label' => 'Статьи', 'url' => ['articles/index']],
                ['label' => 'Блог', 'url' => ['articles/index']],
            ]
        ])?>
    </nav>

<!--    <!-- Navigation -->
<!--    <nav class="clearfix" id="menu">-->
<!--        <ul>-->
<!--            <li><a href="#">Home</a></li>-->
<!--            <li><a href="#">Portfolio</a></li>-->
<!--            <li><a href="#">About</a></li>-->
<!--            <li><a href="#">Contact</a></li>-->
<!--            <li><a href="http://webdesignerhut.com/responsive-layout-with-html-and-css/">back to the tutorial</a></li>-->
<!--        </ul>-->
<!--    </nav>-->

    <!-- Main Content area -->
    <section id="content">
        <h1><?=$this->title?></h1>

        <?=$content?>

    </section>

    <!-- Sidebar -->
    <aside id="sidebar">
        <h3>This is the Sidebar</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua. </p>
    </aside>

    <!-- Navigation -->
    <footer class="clearfix" id="footer">
        Copyright &copy; 2015
    </footer>

</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
