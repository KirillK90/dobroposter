<?php
/* @var $this  View*/
/* @var $filter EventsFilter */

use backend\models\EventsFilter;
use common\components\View;
use common\enums\EventStatus;
use common\models\Event;
use common\models\Format;
use common\models\Place;
use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Управление событиями';
$this->params['breadcrumbs'][] = $this->title;

?>

<?=Html::a("Добавить событие", ['create'], ['class' => 'btn btn-success'])?>
<br><br>

<?= GridView::widget([
    'dataProvider' => $filter->search(),
    'filterModel' => $filter,
    'rowOptions' => function(Event $model) {
        if ($model->status == EventStatus::DRAFT) {
            return ['class' => 'warning'];
        } elseif ($model->status == EventStatus::UNPUBLISHED) {
            return ['class' => 'danger'];
        } else {
            return [];
        }
    },
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'html',
            'value' => function (Event $model) {
                return Html::a($model->id, $model->getUpdateUrl());
            },
            'contentOptions' => [
                'class' => 'id'
            ]
        ],
        [
            'attribute' => 'created_at',
            'filter' => false,
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'title',
            'format' => 'html',
            'value' => function (Event $model) {
                return Html::a($model->title, $model->getUpdateUrl());
            },
        ],
        [
            'attribute' => 'format_id',
            'value' => function(Event $event) {
                return $event->format->name;
            },
            'filter' => Format::getList()
        ],
        [
            'attribute' => 'place_id',
            'value' => function(Event $event) {
                return $event->place->name;
            },
            'filter' => Place::getList()
        ],
        [
            'attribute' => 'start_time',
            'filter' => false,
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'status',
            'value' => function(Event $event) {
                return EventStatus::getName($event->status);
            },
            'filter' => EventStatus::getList()
        ],
        [
            'attribute' => 'created_by',
            'format' => 'html',
            'value' => function (Event $model) {
                return $model->author->username;
            },
            'filter' => User::getPrivilegedList(),
        ],
        [
            'attribute' => 'updated_by',
            'format' => 'html',
            'value' => function (Event $model) {
                return $model->updater->username;
            },
            'filter' => User::getPrivilegedList(),
        ],
        [
            'attribute' => 'published_at',
            'format' => 'datetime',
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
            'contentOptions' => [
                'class' => 'centred'
            ]
        ]
    ],
]);?>