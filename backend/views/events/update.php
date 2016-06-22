<?php
/* @var $this common\components\View */
use yii\helpers\Html;

/* @var $model common\models\Event */

$this->title = 'Редактирование события ' .Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => 'События', 'url' => ['index']];;
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', compact('model')); ?>
