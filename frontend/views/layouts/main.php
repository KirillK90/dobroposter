<?php

/**
 * @var $this View
 * @var $content string
 */

use common\enums\Block;
use frontend\assets\AppAsset;
use frontend\components\View;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru">
<head>
    <title><?=$this->title ." - ".Yii::$app->name  ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<!-- BEGIN BODY -->
<div class="wrap">
<div class="container main-container">
    <header class="header">
        <div class="header-image">
            <div class="brand"><a href="/">Dobroposter</a></div>
            <div class="address-bar">Афиша добрых событий</div>
        </div>
    </header>
    <? NavBar::begin(['options' => ['class' => 'navbar-custom']]); ?>
    <?= Nav::widget([
        'items' => [
            ['label' => 'Главная', 'url' => Yii::$app->homeUrl],
            ['label' => 'О проекте', 'url' => ['/site/about']],
//            ['label' => 'Статьи', 'url' => ['/articles/index']],
            ['label' => 'Контакты', 'url' => ['/site/contacts']],
        ],
        'options' => ['class' => 'navbar-nav'],
    ]); ?>
    <? NavBar::end(); ?>
    <div class="row">
        <div class="col-lg-9">
            <?=$content?>
        </div>
        <div class="col-lg-3">
            <?= $this->renderBlock(Block::RIGHT_SIDEBAR); ?>
        </div>
    </div>

</div>
</div>
<!-- /.container -->

<footer>
    <p class="text-center">Copyright &copy; <?=date('Y')?></p>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
