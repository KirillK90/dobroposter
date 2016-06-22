<?php
/* @var $this  View*/
/* @var $filter UserFilter */

use backend\models\UserFilter;
use common\components\View;
use common\models\Place;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Управление местами';
$this->params['breadcrumbs'][] = $this->title;

?>

<?=Html::a("Новое место", ['create'], ['class' => 'btn btn-success'])?>
<br><br>

<?= GridView::widget([
    'dataProvider' => $filter->search(),
    'filterModel' => $filter,
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'html',
            'value' => function (Place $model) {
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
            'attribute' => 'name',
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