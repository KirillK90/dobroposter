<?php
/**
 * @var array $groupList
 * @var View $this
 */
use common\components\View;
use yii\web\JqueryAsset;

$this->registerJsFile('@static/js/columns.js', ['depends' => JqueryAsset::className()]);
$this->registerJs(<<<JS
    $(".hand-list").multiColumn({amount: 4, overheight: -3});
JS
);
?>

<div class="hand-list">
<div class="column">
    <?php foreach($groupList as $letter => $items): ?>
        <div>
            <div class="letter"><?=$letter?></div>
            <?php $item = array_shift($items);?>
            <div><?=$item?></div>
        </div>

        <?php foreach($items as $item): ?>
        <div><?=$item?></div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
</div>