<?php
/* @var $this  View*/
/* @var $filter ArticleFilter */

use backend\models\ArticleFilter;
use common\components\helpers\Icon;
use common\components\View;
use common\enums\ArticleStatus;
use common\enums\ArticleType;
use common\models\Article;
use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Материары типа "'.ArticleType::getName($filter->type).'"';
$this->params['breadcrumbs'][] = $this->title;

?>

<?=Html::a("Добавить", ['/articles/create', 'type' => $filter->type], ['class' => 'btn btn-success'])?>
<br><br>

<?= GridView::widget([
    'dataProvider' => $filter->search(),
    'filterModel' => $filter,
    'rowOptions' => function(Article $model) {
        if ($model->status == ArticleStatus::DRAFT) {
            return ['class' => 'warning'];
        } elseif ($model->status == ArticleStatus::UNPUBLISHED) {
            return ['class' => 'danger'];
        } else {
            return [];
        }
    },
    'columns' => [
        [
            'attribute' => 'id',
            'header' => '#',
            'contentOptions' => [
                'class' => 'id'
            ]
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{update}',
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'title',
            'format' => 'html',
            'value' => function (Article $model) {
                return Html::a($model->title, $model->getAdminUrl());
            },
        ],
        [
            'attribute' => 'author_id',
            'format' => 'html',
            'value' => function (Article $model) {
                return $model->author->username;
            },
            'filter' => User::getPrivilegedList(),
        ],
        [
            'attribute' => 'updater_id',
            'format' => 'html',
            'value' => function (Article $model) {
                return $model->updater->username;
            },
            'filter' => User::getPrivilegedList(),
        ],
        [
            'attribute' => 'status',
            'value' => function (Article $model) {
                return ArticleStatus::getName($model->status, null);
            },
            'filter' => ArticleStatus::getList(),
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'published_at',
            'format' => 'datetime',
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'updated_at',
            'format' => 'date',
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{view} {delete}',
            'buttons' => [
                'view' => function ($url, Article $model, $key) {
                    return $model->published() ? Html::a(Icon::i('eye-open'), $model->getViewUrl(), ['target' => '_blank']) : '';
                },
            ],
            'contentOptions' => [
                'class' => 'centred'
            ]
        ]
    ],
]);?>