<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "deposit_regions".
 * @property integer $id
 * @property string $_date
 * @property integer $deposit_id
 * @property integer $region_id
 */
class ArchiveDepositRegions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'archive_deposit_regions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','_date','deposit_id', 'region_id'], 'required'],
            [['deposit_id', 'region_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'deposit_id' => 'Deposit ID',
            'region_id' => 'Region ID',
        ];
    }
}
