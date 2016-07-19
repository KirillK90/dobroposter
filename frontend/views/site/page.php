<?php

/* @var $this View */
use common\enums\SeoParam;
use frontend\components\View;

/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

?>
<article class="post">
    <? if ($h1 = $this->getSeo(SeoParam::H1)): ?>
        <div class="box-head">
            <h1 class="box-head__heading heading heading_lvl_1" ><?=$h1 ?></h1>
            <? if ($subTitle = $this->getSeo(SeoParam::SUB_TITLE)): ?>
                <p class="box-head__desc"><?=$subTitle?></p>
            <? endif; ?>
        </div><!--box-head-->
    <? endif; ?>
    <? if ($h2 = $this->getSeo(SeoParam::H2)): ?>
    <div class="box-head box-head_underline">
        <h2 class="heading heading_lvl_2 box-head__heading"><?=$h2 ?></h2>
    </div>
    <? endif; ?>
    <? if ($text = $this->getSeo(SeoParam::TEXT)): ?>
        <div class="post__content"><?=$text?></div>
    <? endif; ?>

</article>


