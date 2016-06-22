<?php
/* @var $this common\components\View */
use yii\helpers\Html;

/* @var $model common\models\Place */

$this->title = 'Редактирование места ' .Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => 'Места', 'url' => ['index']];;
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', compact('model')); ?>
