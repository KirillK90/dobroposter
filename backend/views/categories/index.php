<?php
/* @var $this  View*/
/* @var $filter UserFilter */

use backend\models\UserFilter;
use common\components\View;
use common\models\Category;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Управление категориями';
$this->params['breadcrumbs'][] = $this->title;

?>

<?=Html::a("Новая категория", ['create'], ['class' => 'btn btn-success'])?>
<br><br>

<?= GridView::widget([
    'dataProvider' => $filter->search(),
    'filterModel' => $filter,
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'html',
            'value' => function (Category $model) {
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
            'attribute' => 'primary',
            'format' => 'boolean',
            'filter' => [true => 'Да', false => 'Нет']
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