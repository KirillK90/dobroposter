<?php

use common\enums\Block;
use common\enums\SeoParam;
use common\models\MainIcon;
use frontend\components\View;
use frontend\widgets\TopFilterWidget;

/**
 * @var $this View
 * @var $icons MainIcon[]
 * @var $flashMessages
 */
$icons = MainIcon::find()->orderBy('order')->all();

?>

<? $this->beginBlock(Block::CYAN_TOP); ?>
<?= TopFilterWidget::widget([
    'action' => ['deposits/search', 'regionSlug' => $this->getRegionSlug()],
    'title' => "Вклады и депозиты",
    'amountLabel' => "— Я хочу вложить",
    'shortAmountLabel' => false,
    'applyPjax' => false,
]) ?>
<? $this->endBlock(); ?>

<? $this->beginBlock(Block::WHITE_TOP); ?>
    <div class="best-invest box-best-invest">
        <? if ($h1 = $this->getSeo(SeoParam::H1) ): ?>
        <h1 class="best-invest__title heading heading_lvl_3 heading_striped"><span class="heading__text"><?=$h1 ?></span></h1>
        <? endif; ?>
        <ul class="best-invest__icons">
            <? foreach($icons as $icon): ?>
                <li class="best-invest__icon">
                    <div class="icon-invest">
                        <div class="icon">
                            <a href="<?=$icon->url?>"><img src="<?=$icon->getSrc()?>" class="icon-img" data-hover="<?=$icon->getSrc(true)?>"/></a>
                        </div>
                    </div>
                    <p class="best-invest__ico-desc"><?=$icon->title ?></p>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
<? $this->endBlock(); ?>
<? if(isset($passwordForm)): ?>
    <?=$this->render('_passChangeResetModal',compact('passwordForm')) ?>
<? endif; ?>

