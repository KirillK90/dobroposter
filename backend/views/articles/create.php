<?php
/* @var $this common\components\View */

/* @var $model common\models\Article */

$this->title = 'Новый материал';
$this->params['breadcrumbs'][] = ['label' => $model->getTypeName(), 'url' => ['/articles/index', 'type' => $model->type]];
$this->params['breadcrumbs'][] = 'Добавление';
?>

<?= $this->render('_form', compact('model')); ?>