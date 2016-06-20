<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bank_data".
 *
 * @property integer $id
 * @property string $review
 * @property string $reg_date
 * @property integer $reli_rate
 * @property integer $active_money_rate
 * @property integer $redirect_bank_id
 *
 * relations
 * @property Bank $redirectBank
 */
class BankData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bank_data';
    }

    public function getRedirectBank()
    {
        return $this->hasOne(Bank::className(), ['id' => 'redirect_bank_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reg_date', 'review'], 'safe'],
            [['reli_rate', 'active_money_rate', 'redirect_bank_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'review' => 'Описание банка',
            'reg_date' => 'Дата регистрации',
            'reli_rate' => 'Рейтинг надежности',
            'active_money_rate' => 'Рейтинг активов',
            'redirect_bank_id' => 'Редирект на другой банк',
        ];
    }
}
