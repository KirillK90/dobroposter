<?php
/* @var $this common\components\View */
use yii\helpers\Html;

/* @var $model common\models\User */

$this->title = 'Редактирование пользователя ' .Html::encode($model->username);
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/users']];;
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', compact('model')); ?>
