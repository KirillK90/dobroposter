<?php
/* @var $this common\components\View */
use yii\helpers\Html;

/* @var $model common\models\Format */

$this->title = 'Редактирование пользователя ' .Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => 'Форматы', 'url' => ['index']];;
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', compact('model')); ?>
