<?php

/**
 * @var $this View
 * @var $currency
 * @var $period
 * @var $middleRate
 * @var $maxRateDeposit Deposit
 * @var $maxRatingDeposit Deposit
 */
use common\components\helpers\HStrings;
use common\models\Deposit;
use frontend\components\View;

$maxRateDepositCurr = $maxRateDeposit->getDepositCurrency($currency);
$maxRatingDepositCurr = $maxRatingDeposit->getDepositCurrency($currency);
?>
    <div class="best-today__col">
        <div class="best-today__header">Cредняя ставка</div>
        <div class="best-today__middle-rate"><?= HStrings::rate($middleRate)?></div>
    </div>
    <div class="best-today__col">
        <div class="best-today__header">Максимальная ставка</div>
        <div class="best-today__deposit-rate"><?= HStrings::rate($maxRateDepositCurr->getMaxRate())?></div>
        <a href="<?=$maxRateDeposit->getUrl($this->getRegionSlug(), $currency, $period ? $maxRateDepositCurr->getRealPeriod($period) : null)?>" class="best-today__deposit-name">Вклад &laquo;<?=$maxRateDeposit->product_name?>&raquo;</a>
        <a class="best-today__deposit-bank" href="<?=$maxRateDeposit->bank->getUrl($this->getRegionSlug())?>"><?=$maxRateDeposit->bank->name?></a>
    </div>
    <div class="best-today__col">
        <div class="best-today__header">С высшим рейтингом</div>
        <div class="best-today__deposit-rate"><?= HStrings::rate($maxRatingDepositCurr->getMaxRate())?></div>
        <a href="<?=$maxRatingDeposit->getUrl($this->getRegionSlug(), $currency, $period ? $maxRatingDepositCurr->getRealPeriod($period) : null)?>" class="best-today__deposit-name">Вклад &laquo;<?=$maxRatingDeposit->product_name?>&raquo;</a>
        <a class="best-today__deposit-bank" href="<?=$maxRateDeposit->bank->getUrl($this->getRegionSlug())?>"><?=$maxRatingDeposit->bank->name?></a>
    </div>
