<?php
use backend\assets\AppAsset;
use common\components\helpers\Icon;
use common\enums\ArticleType;
use common\enums\UserRole;
use frontend\widgets\Alert;
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

                if ($this->getUser()->role == UserRole::EDITOR) {
                    $mainMenuItems = [
                        ['label' => 'Страницы', 'url' => ['/articles/index', 'type' => ArticleType::PAGE]],
                        ['label' => 'Новости', 'url' => ['/articles/index', 'type' => ArticleType::NEWS]],
                        ['label' => 'Аналитика', 'url' => ['/articles/index', 'type' => ArticleType::ANALYTICS]],
                        ['label' => 'Гид по вкладам', 'url' => ['/articles/index', 'type' => ArticleType::GUIDES]],
                        ['label' =>  Icon::i('plus').' Добавить', 'url' => ['/articles/new']],

                    ];
                } else {

                    $mainMenuItems = [
                        ['label' => 'Управление вкладами', 'items' => [
                            ['label' => 'Банки и вклады', 'url' => ['/banks']],
                            ['label' => 'Структура вкладов', 'url' => '/structure'],
                            ['label' => 'Рейтинг вкладов', 'url' => '/ratings'],
                        ]],
                        ['label' => 'Города', 'url' => ['/regions']],
                        ['label' => 'Справочники', 'url' => ['/helpers']],
                        ['label' => 'UI-блоки', 'items' => [
                            ['label' => 'Баннеры', 'url' => ['/page-banners']],
                            ['label' => 'Слайдеры со вкладами', 'url' => '/sliders'],
                            ['label' => 'Блоки с подборками вкладов', 'url' => '/blocks'],
                            ['label' => 'Кастомизация иконок на главной', 'url' => ['/icons']]
                        ]],
                        ['label' => 'Материалы', 'items' => [
                            ['label' => 'СЕО', 'url' => ['/seo']],
                            ['label' => 'Страницы', 'url' => ['/articles/index', 'type' => ArticleType::PAGE]],
                            ['label' => 'Новости', 'url' => ['/articles/index', 'type' => ArticleType::NEWS]],
                            ['label' => 'Аналитика', 'url' => ['/articles/index', 'type' => ArticleType::ANALYTICS]],
                            ['label' => 'Гид по вкладам', 'url' => ['/articles/index', 'type' => ArticleType::GUIDES]],
                            ['label' =>  Icon::i('plus').' Добавить', 'url' => ['/articles/new']],
                        ]],
                        ['label' => 'Синхронизация', 'url' => ['/api']],
                        ['label' => 'Пользователи', 'url' => '/users'],
                    ];

                }
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
