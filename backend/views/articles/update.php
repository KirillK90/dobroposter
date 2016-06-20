<?php
use yii\helpers\Html;

/* @var $this common\components\View */
/* @var $model common\models\Article */

$this->title = Html::encode($model->title).($model->preview ? ' (Предпросмотр)' : '');
$this->params['breadcrumbs'][] = ['label' => $model->getTypeName(), 'url' => ['/articles/index', 'type' => $model->type]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', compact('model')); ?>
