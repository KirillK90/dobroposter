<?php
/**
 * Created by PhpStorm.
 * User: omega
 * Date: 7/11/15
 * Time: 1:51 AM
 */

namespace common\models;


use common\components\helpers\HDates;
use common\components\helpers\HStrings;
use common\enums\Currency;
use common\enums\RatePeriod;
use yii\base\Object;
use yii\helpers\ArrayHelper;

/**
 * @property string ratingClass
 */
class DepositCurrency extends Object
{
    /** @var  string */
    public $currency;

    /** @var  Deposit */
    public $deposit;

    /** @var  DepositRate[] */
    public $rates;

    public $rating;

    public function hasDiscretePeriods()
    {
        $rate = reset($this->rates);
        return $rate->period_from == $rate->period_to && !$this->hasFixedPeriod();
    }

    public function hasFixedPeriod()
    {
        return $this->getMinPeriod() == $this->getMaxPeriod();
    }

    public function getDiscretePeriodsNotations()
    {
        $notations = [];
        foreach ($this->rates as $rate) {
            $notations[$rate->period_from] = HDates::period($rate->period_from_notation, false, true);
        }
        ksort($notations);
        return $notations;
    }

    public function getDiscretePeriodsArrayForCalc()
    {
        $notations = [];

        foreach ($this->rates as $rate) {
            $notations[$rate->period_from] = ['rus'=>HDates::period($rate->period_from_notation, false, true),'original'=>$rate->period_from_notation];
        }
        ksort($notations);
        return $notations;
    }

    public function getDefaultAmount()
    {
        return max($this->getMinAmount(), Currency::getDefaultAmount($this->currency));
    }

    public function getDefaultPeriod($inMonths = false)
    {
        if ($this->hasFixedPeriod()) {
            $defaultPeriod = ArrayHelper::getValue($this->rates, '0.period_from');
        } else {
            $defaultPeriod = $this->getMaxPeriod(RatePeriod::toDays(RatePeriod::FIVE_YEARS));
        }
        return $inMonths ? round($defaultPeriod / 30) : $defaultPeriod;
    }

    public function getPeriodsStr()
    {
        $periodsFrom = [];
        $periodsTo = [];
        $from = $to = true;
        foreach ($this->rates as $rate) {
            if ($rate->period_from) {
                $periodsFrom[] = $rate->period_from;
            } else {
                $from = false;
            }
            if ($rate->period_to) {
                $periodsFrom[] = $rate->period_to;
            } else {
                $to = false;
            }
        }

        $str = '';
        if ($from && $periodsFrom) {
            $from = min($periodsFrom);
            $str .= "От $from ";
        }
        if ($to && $periodsTo) {
            $to = max($periodsTo);
            $str .= "до $to ";
        }
        if ($str) {
            $str .= "дней";
        } else {
            $str = 'безсрочный';
        }

        return $str;
    }

    public function getMaxRate()
    {
        $maxRate = 0;
        $rates = $this->rates;
        foreach ($rates as $rate) {
            if ($rate->rate_max > $maxRate) {
                $maxRate = $rate->rate_max;
            }
        }
        return $maxRate;
    }

    public function getMinRate()
    {
        $rates = [];
        foreach ($this->rates as $rate) {
            $rates[] = $rate->rate_min;
            $rates[] = $rate->rate_max;
        }
        return min($rates);
    }

    public function getMaxRateStr()
    {
        $maxRate = $this->getMaxRate();
        return $this->hasFixedRate() ? HStrings::rate($maxRate) : "до ".HStrings::rate($maxRate);
    }

    public function hasFixedRate()
    {
        $rates = $this->rates;
        $rateVals = [];
        foreach ($rates as $rate) {
            $rateVals[] = $rate->rate_min;
            $rateVals[] = $rate->rate_max;
        }
        return min($rateVals) == max($rateVals);
    }

    public function getMinAmountStr($period = null, $default = 'n/a')
    {
        $minAmount = $this->getMinAmount($period);
        return $minAmount ? "от " . HStrings::currency($minAmount) : $default;
    }

    public function getMinAmount($period = null)
    {
        $minSum = null;
        $rates = $this->rates;
        foreach ($rates as $rate) {
            if (!$period || $rate->hasPeriod($period)) {
                if (is_null($minSum) || $rate->amount_from < $minSum) {
                    $minSum = $rate->amount_from;
                }
            }
        }
        return $minSum;
    }

    public function getMaxAmount($default = 'n/a')
    {
        $maxAmount = 0;
        $rates = $this->rates;
        if (!$rates) return null;
        foreach ($rates as $rate) {
            if ($rate->amount_to > $maxAmount) {
                $maxAmount = $rate->amount_to;
            }
        }
        return $maxAmount ? $maxAmount : $default;
    }

    public function getMinPeriodNotation()
    {
        $minPeriod = null;
        $minPeriodNotation = null;
        $rates = $this->rates;
        if (!$rates) return 'n/a';
        foreach ($rates as $rate) {
            if (!$minPeriod || $rate->period_from < $minPeriod) {
                $minPeriod = $rate->period_from;
                $minPeriodNotation = $rate->period_from_notation;
            }
        }
        return $this->hasFixedPeriod() ? HDates::period($minPeriodNotation) : "от " . HDates::period($minPeriodNotation, true);
    }

    public function getPeriods($from = true)
    {
        $rates = $this->rates;
        $periods = [];
        foreach ($rates as $rate) {
            if ($from && $rate->period_from) {
                $periods[] = $rate->period_from;
            }
            if (!$from && $rate->period_to) {
                $periods[] = $rate->period_to;
            }
        }

        return $periods;
    }

    public function getMinPeriod()
    {
        $fromPeriods = $this->getPeriods(true);
        return $fromPeriods ? min($fromPeriods) : null;
    }

    public function getMinPeriodInMonths()
    {
        $period = $this->getMinPeriod();
        return round($period / 30);
    }

    public function getMaxPeriod($default = 'n/a')
    {
        $toPeriods = $this->getPeriods(false);
        return $toPeriods ? max($toPeriods) : $default;
    }

    public function getMaxPeriodInMonths($default = 'n/a')
    {
        $period = $this->getMaxPeriod($default);
        return $period ? $this->inMonths($period) : $default;
    }

    public function inMonths($period)
    {
        return round($period / 30);
    }

    public function getGroupedRates()
    {
        $periods = $this->getRatesPeriods();
        $gropedRates = ArrayHelper::map($this->rates, 'periodNotation', 'rate_max', 'amountStr');
        foreach ($gropedRates as $amount => &$rates) {
            foreach($periods as $period) {
                if (!isset($rates[$period])) {
                    $rates[$period] = null;
                }
            }
        }

        return $gropedRates;
    }

    public function getRatesByPeriod()
    {
        $rates = [];
        foreach ($this->rates as $rate) {
            $rates[$rate->period_from][$rate->amount_from] = $rate->rate_max;
        }

        return $rates;
    }

    public function getMiddleRatesByEnumPeriod()
    {
        $rates = [];
        foreach ($this->rates as $rate) {
            $rates[$rate->getEnumPeriod()][] = $rate->getMiddleRate();
        }
        $avgRates = [];
        foreach($rates as $period => $values) {
            $avgRates[$period] = round(array_sum($values) / count($values), 4);
        }

        return $avgRates;
    }

    public function getRatesPeriods()
    {
        $periods = [];
        foreach($this->rates as $rate) {
            $periods[] = $rate->getPeriodNotation();
        }
        return array_unique($periods);
    }

    public function getRate($period, $amount)
    {
        $maxRates = null;
        foreach ($this->rates as $rate) {
            if ((!$period || $rate->hasPeriod($period)) && (!$amount || $rate->hasAmount($amount))) {
                $maxRates[] = $rate->rate_max;
            }
        }
        return $maxRates ? max($maxRates) : null;
    }

    public function getRateStr($period, $amount, $default = 'n/a')
    {
        $maxRate = $this->getRate($period, $amount);
        return $maxRate ? HStrings::rate($maxRate, !$period) : $default;
    }

    public function getRatingClass()
    {
        return "raiting_".DepositRating::getRatingNumber($this->rating);
    }

    public function getRealPeriod($period)
    {
        $realPeriod = RatePeriod::toDays($period);
        if ($this->hasDiscretePeriods()) {
            foreach($this->rates as $rate) {
                if ($rate->hasPeriod($period)) {
                    $realPeriod = $rate->period_from;
                }
            }
        } elseif ($this->hasFixedPeriod()) {
            $realPeriod = $this->getMinPeriod();
        }

        return $realPeriod;
    }

    public function getMaxPeriodStr()
    {
        return RatePeriod::getMaxStr($this->getMaxPeriod());
    }
}