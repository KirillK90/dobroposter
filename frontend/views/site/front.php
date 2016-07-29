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
        'pager' => [
            'pagination' => ['route' => '/'],
            'options' => ['class' => 'pagination pagination-lg']
        ],
        'layout' => "{items}\n<div class=\"col-md-12 text-center\">{pager}</div>"
    ])?>
</div>

