<?php
/* @var $this  View*/
/* @var $filter UserFilter */

use backend\models\UserFilter;
use common\components\View;
use common\enums\Gender;
use common\enums\OAuthName;
use common\enums\UserRole;
use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Управление пользователями';
$this->params['breadcrumbs'][] = $this->title;

?>

<?=Html::a("Новый пользователь", ['/users/create'], ['class' => 'btn btn-success'])?>
<br><br>

<?= GridView::widget([
    'dataProvider' => $filter->search(),
    'filterModel' => $filter,
    'rowOptions' => function(User $model) {
        if ($model->status == User::STATUS_DELETED) {
            return ['class' => 'danger'];
        } else {
            return [];
        }
    },
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'html',
            'value' => function (User $model) {
                return Html::a($model->id, $model->getAdminUrl());
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
            'attribute' => 'last_visit',
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'username',
        ],
        [
            'attribute' => 'email',
        ],
        [
            'attribute' => 'is_subscribed',
            'format' => 'boolean',
            'filter' => [true => 'Да', false => 'Нет']
        ],
        [
            'attribute' => 'role',
            'value' => function (User $model) {
                return $model->role ? UserRole::getName($model->role) : '';
            },
            'filter' => UserRole::getList(),
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'gender',
            'value' => function (User $model) {
                return Gender::getName($model->gender, null);
            },
            'filter' => Gender::getList(),
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'oauth',
            'format' => 'boolean',
            'filter' => [false => 'Нет', true => 'Да'],
            'contentOptions' => [
                'class' => 'centred'
            ]
        ],
        [
            'attribute' => 'oauth_service',
            'value' => function (User $model) {
                return OAuthName::getName($model->oauth_service, null);
            },
            'filter' => OAuthName::getList(),
        ],
        [
            'attribute' => 'oauth_id',
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