<?php
use backend\assets\AppAsset;
use backend\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this \common\components\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?=$this->title ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            if (Yii::$app->user->isGuest) {
                $mainMenuItems = [];
                $userMenuItems = [];
            } else {
                $userMenuItems = [[
                    'label' => 'Выход (' . $this->getUser()->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ]];

                $mainMenuItems = [
                    ['label' => 'События', 'url' => ['/events/index']],
                    ['label' => 'Категории', 'url' => ['/categories/index']],
                    ['label' => 'Места', 'url' => ['/places/index']],
                    ['label' => 'Пользователи', 'url' => ['/users/index']],
                ];
            }

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'],
                'items' => $mainMenuItems,
                'encodeLabels' => false
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $userMenuItems,
                'encodeLabels' => false
            ]);
            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'encodeLabels' => false,
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
            <div class="content">
                <h1><?= $this->title ?></h1>
                <?= $content ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; <?=Yii::$app->name?> <?= date('Y') ?></p>
<!--        <p class="pull-right">Powered by <a href="http://polygant.ru/" rel="external">Polygant</a></p>-->
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
