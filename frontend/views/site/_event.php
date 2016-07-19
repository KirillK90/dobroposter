<?php

use common\models\Event;
use frontend\components\View;
use yii\helpers\Url;

/**
 * @var $this View
 * @var Event $model
 */
$this->title = 'Афиша Добрых Событий'
?>

<div class="col-md-4">
    <div class="event-preview">
        <div class="event-preview__header">
            <a href="<?=Url::to(['/events/view', 'id' => $model->id])?>">
                <img class="img-responsive" src="<?=$model->getImageSrc(true)?>" alt="<?=$model->title?>" />
            </a>
        </div>
        <div class="event-preview__body">
            <div class="event-preview__text">
                <h2 class="event-preview__title text-center"><?=$model->title?></h2>
                <?=$model->announcement?>
            </div>
            <a href="<?=Url::to(['/events/view', 'id' => $model->id])?>" class="btn btn-default event-preview__readmore">Читать далее</a>
        </div>
    </div>
</div>