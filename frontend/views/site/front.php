<?php

use frontend\components\View;
use frontend\models\EventsFilter;
use yii\widgets\ListView;

/**
 * @var $this View
 * @var $model EventsFilter
 */
$this->title = 'Афиша Добрых Событий'
?>
<div class="row">
    <?=ListView::widget([
        'dataProvider' => $model->search(),
        'itemView' => '_event',
        'layout' => "{items}\n{pager}"
    ])?>
</div>

