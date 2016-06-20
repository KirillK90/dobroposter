<?php

namespace common\models;

use common\enums\Agency;
use Yii;

/**
 * This is the model class for table "agency_rating".
 *
 * @property integer $id
 * @property string $active_from
 * @property integer $iblock_id
 * @property string $iblock_code
 * @property integer $bank_id
 * @property string $bank_name
 * @property string $national_scalerating_value
 * @property integer $national_scalerating_enum_id
 */
class AgencyRating extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency_rating';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'active_from', 'iblock_id', 'iblock_code', 'bank_id', 'bank_name'], 'required'],
            [['id', 'iblock_id', 'bank_id', 'national_scalerating_enum_id'], 'integer'],
            [['active_from'], 'safe'],
            [['iblock_code', 'bank_name', 'national_scalerating_value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active_from' => 'Active From',
            'iblock_id' => 'Iblock ID',
            'iblock_code' => 'Iblock Code',
            'bank_id' => 'Bank ID',
            'bank_name' => 'Bank Name',
            'national_scalerating_value' => 'National Scalerating Value',
            'national_scalerating_enum_id' => 'National Scalerating Enum ID',
        ];
    }

    public function isRus()
    {
        return Agency::isRus($this->iblock_id);
    }
}
