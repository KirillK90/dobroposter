<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "top_city".
 *
 * @property integer $id
 * @property integer $region_id
 */
class TopCity extends \yii\db\ActiveRecord
{
    public $city_ids;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'top_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id'], 'required'],
            [['city_ids'], 'required', 'on' => 'form-insert'],
            [['region_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
        ];
    }
}
