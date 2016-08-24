<?php

use common\enums\Block;
use common\models\Category;
use frontend\components\View;
use frontend\models\EventsFilter;
use yii\helpers\Url;

/**
 * @var $this View
 * @var $model EventsFilter
 */
$this->title = 'Афиша Добрых Событий'

?>
<div class="row" id="events-list">
    <?=$this->render('_events', ['model' => $model])?>
</div>

<? $this->beginBlock(Block::RIGHT_SIDEBAR); ?>
<div class="block">
    <h6 class="block-title">Категории</h6>
    <div class="block-body">
        <ul>
            <?foreach(Category::getCountList() as $id => $name): ?>
                <li><a class="ajax-filter" href="<?= Url::to(["site/filter", 'category_id' => $id])?>"><?=$name?></a></li>
            <?endforeach; ?>
        </ul>
    </div>
</div>
<? $this->endBlock() ?>

<?
$this->registerJs(<<<JS
$(".ajax-filter").click(function(e){
    e.preventDefault();
    console.log($(this).attr('href'));
    $("#events-list").load($(this).attr('href'));
    return false;
})
JS
);