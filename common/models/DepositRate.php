<?php

namespace common\models;

use common\components\helpers\HDates;
use common\components\helpers\HStrings;
use common\enums\RatePeriod;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rate".
 *
 * @property integer $id
 * @property integer $deposit_id
 * @property string $currency_id
 * @property integer $amount_from
 * @property integer $amount_to
 * @property integer $period_from
 * @property integer $period_to
 * @property string $period_from_notation
 * @property string $period_to_notation
 * @property double $rate_min
 * @property double $rate_max
 * @property integer $rate_type_id
 * @property integer $finance_indicator_id
 * @property double $finance_indicator_multiplier
 * @property string $finance_indicator_multiplier_notation
 * @property integer $fixation
 * @property string $fixation_notation
 * @property double $base_float_rate_min
 * @property double $base_float_rate_max
 * @property double $float_rate_min
 * @property double $float_rate_max
 * @property double $rate_restriction_min
 * @property double $rate_restriction_max
 */
class DepositRate extends ActiveRecord
{
    const PERIOD_PRECISION = 20; // 5 дней

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deposit_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'deposit_id', 'currency_id', 'rate_type_id'], 'required'],
            [['deposit_id', 'amount_from', 'amount_to', 'period_from', 'period_to', 'rate_type_id', 'fixation'], 'integer'],
            [['rate_min', 'rate_max', 'finance_indicator_multiplier', 'base_float_rate_min', 'base_float_rate_max', 'float_rate_min', 'float_rate_max', 'rate_restriction_min', 'rate_restriction_max'], 'number'],
            [['currency_id', 'period_from_notation', 'period_to_notation', 'finance_indicator_multiplier_notation', 'fixation_notation', 'finance_indicator_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => 'Сумма',
            'period' => 'Период',
            'rate' => 'Ставка',
            'deposit_id' => 'ID депозита',
            'currency_id' => 'Валюта',
            'amount_from' => 'Сумма от',
            'amount_to' => 'Сумма до',
            'period_from' => 'Срок от',
            'period_to' => 'Срок до',
            'period_from_notation' => 'Срок от',
            'period_to_notation' => 'Срок до',
            'rate_min' => 'Ставка от',
            'rate_max' => 'Ставка до',
            'rate_type_id' => 'Тип ставок',
            'finance_indicator_id' => 'ID финансового индикатора',
            'finance_indicator_multiplier' => 'Мультипликатор финансового мультипликатора',
            'finance_indicator_multiplier_notation' => 'Мультипликатор финансового мультипликатора',
            'fixation' => 'Срок действия фиксированной ставки для комбинированных ставок',
            'fixation_notation' => 'Срок действия фиксированной ставки для комбинированных ставок',
            'base_float_rate_min' => 'Базовая плавающая процентная ставка (от)',
            'base_float_rate_max' => 'Базовая плавающая процентная ставка (до)',
            'float_rate_min' => 'Плавающая процентная ставка с учетом финансового индикатора (от)',
            'float_rate_max' => 'Плавающая процентная ставка с учетом финансового индикатора (до)',
            'rate_restriction_min' => 'Ограничение плавающей процентной ставки (от)',
            'rate_restriction_max' => 'Ограничение плавающей процентной ставки (до)',
        ];
    }

    public function getPeriodNotation($glue = '<br>')
    {
        if ($this->hasFixedPerid()) {
            return HDates::period($this->period_from_notation);
        } else {
            $parts = [];
            if ($this->period_from_notation) {
                $parts[] = "от ".HDates::period($this->period_from_notation, true);
            }
            if ($this->period_to_notation) {
                $parts[] = "до ".HDates::period($this->period_to_notation, true);
            }
            return implode($glue, $parts);
        }
    }

    public function hasFixedPerid()
    {
        return $this->period_from == $this->period_to;
    }

    public function hasFixedAmount()
    {
        return $this->amount_from == $this->amount_to;
    }

    public function getAmountStr()
    {
        if ($this->hasFixedAmount()) {
            return HStrings::currency($this->amount_from);
        } else {
            $parts = [];
            if ($this->amount_from) {
                $parts[] = "от ".HStrings::currency($this->amount_from);;
            }
            if ($this->amount_to) {
                $parts[] = "до ".HStrings::currency($this->amount_to);;
            }
            return implode(' ', $parts);
        }
    }

    public function getDeposit()
    {
        return $this->hasOne(Deposit::className(), ['id' => 'deposit_id']);
    }

    public function hasPeriod($period)
    {
        list($periodMin, $periodMax) = RatePeriod::toDaysPrecision($period);
        return (!$this->period_from || $this->period_from <= $periodMax) && (!$this->period_to || $periodMin <= $this->period_to);
    }

    public function hasAmount($amount)
    {
        return (!$this->amount_from || $this->amount_from <= $amount) && (!$this->amount_to || $amount <= $this->amount_to);
    }

    public function getMiddleRate()
    {
        return ($this->rate_min + $this->rate_max) / 2;
    }

    public function getEnumPeriod()
    {
        return RatePeriod::getValue($this->period_from);
    }
}
