<?php

namespace common\models;

use Yii;
use yii\sphinx\ActiveQuery;

/**
 * This is the model class for table "deposit_rating".
 *
 * @property integer $deposit_id
 * @property integer $currency_id
 * @property integer $value
 * @property string $details
 * @property Deposit deposit
 * @property Bank bank
 * @property DepositRate[] rates;
 * @property FolkRating folkRating
 * @property DepositParams[] params;
 *
 */
class DepositRating extends \yii\db\ActiveRecord
{
    public $max_rate;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deposit_rating';
    }

    public static function getRatingNumber($value)
    {
        if ($value >= 70) {
            return 1;
        } elseif ($value >= 40 and $value < 70) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deposit_id', 'currency_id', 'value'], 'required'],
            [['deposit_id', 'value'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'deposit_id' => 'Deposit ID',
            'value' => 'Значение',
        ];
    }

    public function getDeposit()
    {
        return $this->hasOne(Deposit::className(), ['id' => 'deposit_id']);
    }

    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['id' => 'bank_id'])->via('deposit');
    }

    public function getRates()
    {
        return $this->hasMany(DepositRate::className(), ['deposit_id' => 'deposit_id', 'currency_id' => 'currency_id']);
    }


    public function getParams()
    {
        return $this->hasMany(DepositParams::className(), ['deposit_id' => 'deposit_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAgencyRatings()
    {
        return $this->hasMany(AgencyRating::className(), ['bank_id' => 'id'])->via('bank')
            ->indexBy('iblock_id')->orderBy('active_from');
    }

    /**
     * @return ActiveQuery
     */
    public function getFolkRating()
    {
        return $this->hasOne(FolkRating::className(), ['bank_id' => 'id'])->via('bank');
    }

    public function getDepositCurrency()
    {
        return new DepositCurrency(['deposit' => $this->deposit, 'rates' => $this->rates, 'currency' => $this->currency_id]);
    }
}
