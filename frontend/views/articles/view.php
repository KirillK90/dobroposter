<?php
/**
 * @var $this View
 * @var $model Article
 */

use common\components\helpers\HStrings;
use common\enums\Block;
use common\enums\SeoParam;
use common\models\Article;
use common\models\Deposit;
use frontend\components\View;
use frontend\widgets\BestTabsWidget;
use frontend\widgets\DepositsBlockWidget;
use yii\helpers\Url;
use yii\web\JqueryAsset;

if ($model->isArticle()) {
    $this->params['breadcrumbs'][] = ['label' => $model->getTypeName(), 'url' => ['/articles/index', 'type' => $model->type]];
}
if ($breadcrumb = $this->getSeo(SeoParam::BREADCRUMB)) {
    $this->params['breadcrumbs'][] = $this->getSeo(SeoParam::BREADCRUMB);
}

if ($model->preview) {
    $this->registerJsFile('@static/js/iframeResizer.contentWindow.min.js');
}

$this->registerMetaTag([
    'name' => 'og:description',
    'content' => $model->getAnnouncement()
]);
$this->registerMetaTag([
    'name' => 'og:image',
    'content' => HStrings::addHttp($model->getImageSrc() ?: $this->getLogoUrl())
]);

//Не показывать блок "Лучшее в разделах"
$this->params['showBestTabs'] = false;

$this->registerJsFile('@static/js/news_functions.js',['depends' => [JqueryAsset::className()]]);
?>
<? $this->beginBlock(Block::RIGHT_CENTER); ?>
<?=DepositsBlockWidget::widget([
    'header' => 'Вклады с высшим рейтингом',
    'deposits' => Deposit::getBest(),
]) ?>
<br>
<a class="all-categories" href="<?=Url::to(['deposits/search'])?>">Все Предложения</a>
<? $this->endBlock(); ?>

<article class="post post_size_full post_single">
        <div class="box-head box-head_without-padding">
            <h1 class="box-head__heading heading heading_lvl_1" ><?=$model->title ?></h1>
        </div><!--box-head-->
    <div class="post-meta">
        <? if ($this->isPrivilegedUser()): ?>
            <div class="post__author"><?=$model->author->username ?></div>
        <? endif; ?>

        <div class="post__date">
        <? if ($model->published_at): ?>
            <?=Yii::$app->formatter->asDate($model->published_at, 'd MMMM y')?>
            <span class="post__time"><?=Yii::$app->formatter->asTime($model->published_at, 'short')?></span>
        <? endif; ?>
        <? if (!$model->published_at): ?>
            (Не опубликовано)
        <? endif; ?>
        </div>

        <? if ($this->isPrivilegedUser()): ?>
            <div class="post-views post-counter">
                <i class="post-conter__icon icon icon_post_views icon_size_s"></i>
                <span class="post-conter__count post-views__count"><?=$model->views_count?></span>
            </div>
            <a class="post__edit-link" href="<?=$model->getUpdateLink() ?>">Править</a>
        <? endif; ?>

                    <a class="post-comments post-counter" href="#comments-count">
                        <i class="post-counter__icon icon icon_post_comments icon_size_s"></i>
                        <span class="post-counter__count post-comments__count"></span>
                    </a>

    </div>

    <? if ($model->image_src): ?>
    <div class="post-img">
        <img class="post-img__img image" src="<?=$model->getImageSrc()?>">
        <? if ($model->image_description): ?>
        <div class="post-img__desc"><?=$model->image_description ?></div>
        <? endif; ?>
        <? if ($model->image_source): ?>
        <div class="post-img__source">Фото: <?=$model->getImageSource()?></div>
        <? endif; ?>
    </div>
    <? endif; ?>

    <div class="post__body<?=$model->likes_enabled ? ' post_has-sidebar' : ''?>">
        <? if ($model->likes_enabled): ?>
            <aside class="post__aside post__aside_left">
                <div class="social-likes social-likes_vertical">
                    <div class="facebook" title="Поделиться ссылкой на Фейсбуке"></div>
                    <div class="vkontakte" title="Поделиться ссылкой во Вконтакте"></div>
                    <div class="twitter" title="Поделиться ссылкой в Твиттере"></div>
                </div>
            </aside>
        <? endif; ?>

        <section class="post__content">
            <? if ($model->sub_title): ?>
                <blockquote><?=$model->sub_title?></blockquote>
            <? endif; ?>
            <?=$model->text?>
            <? if ($model->banks): ?>
                <p class="post__notes">
                    Банки: <?=implode(', ', $model->getBanksUrls())?>
                </p>
            <? endif; ?>
            <? if ($model->depositCategories): ?>
                <p class="post__notes">
                    Вклады: <?=implode(', ', $model->getDepositCategoriesUrls())?>
                </p>
            <? endif; ?>
        </section>
        <?=$this->render('_footer', ['model' => $model])?>
    </div>
</article>

<?= BestTabsWidget::widget() ?>
