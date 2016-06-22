<?php
/* @var $this common\components\View */

$this->title = 'Новая категория';
$this->params['breadcrumbs'][] = ['label' => 'Форматы', 'url' => ['index']];;
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', compact('model')); ?>