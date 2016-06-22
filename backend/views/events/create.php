<?php
/* @var $this common\components\View */

$this->title = 'Новое событие';
$this->params['breadcrumbs'][] = ['label' => 'События', 'url' => ['index']];;
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', compact('model')); ?>