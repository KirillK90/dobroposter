<?php
/* @var $this common\components\View */

$this->title = 'Новое место';
$this->params['breadcrumbs'][] = ['label' => 'Места', 'url' => ['index']];;
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', compact('model')); ?>