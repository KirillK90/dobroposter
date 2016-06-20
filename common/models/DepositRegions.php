<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "deposit_regions".
 *
 * @property integer $deposit_id
 * @property integer $region_id
 */
class DepositRegions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deposit_regions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deposit_id', 'region_id'], 'required'],
            [['deposit_id', 'region_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'deposit_id' => 'Deposit ID',
            'region_id' => 'Region ID',
        ];
    }
}
