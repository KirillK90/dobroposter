<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bank_object".
 *
 * @property integer $id
 * @property string $type
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property boolean $active
 * @property boolean $is_main_office
 * @property string $metro_name
 * @property boolean $without_weekend
 * @property string $schedule_general
 * @property string $schedule_private_person
 * @property string $schedule_entities
 * @property string $additional
 * @property integer $bank_id
 * @property string $schedule_vip
 * @property string $comment_to_address
 * @property boolean $is_round_the_clock
 * @property boolean $is_works_as_shop
 * @property boolean $is_at_closed_place
 * @property double $longitude
 * @property double $latitude
 * @property integer $region_id
 */
class BankObject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bank_object';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'name', 'address', 'active', 'is_main_office', 'without_weekend', 'bank_id', 'is_round_the_clock', 'is_works_as_shop', 'is_at_closed_place', 'region_id'], 'required'],
            [['active', 'is_main_office', 'without_weekend', 'is_round_the_clock', 'is_works_as_shop', 'is_at_closed_place'], 'boolean'],
            [['bank_id', 'region_id'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['schedule_general', 'schedule_private_person', 'schedule_entities', 'additional', 'schedule_vip', 'comment_to_address'], 'safe'],
            [['type', 'name', 'address', 'phone', 'metro_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'address' => 'Address',
            'phone' => 'Phone',
            'active' => 'Active',
            'is_main_office' => 'Is Main Office',
            'metro_name' => 'Metro Name',
            'without_weekend' => 'Without Weekend',
            'schedule_general' => 'Schedule General',
            'schedule_private_person' => 'Schedule Private Person',
            'schedule_entities' => 'Schedule Entities',
            'additional' => 'Additional',
            'bank_id' => 'Bank ID',
            'schedule_vip' => 'Schedule Vip',
            'comment_to_address' => 'Comment To Address',
            'is_round_the_clock' => 'Is Round The Clock',
            'is_works_as_shop' => 'Is Works As Shop',
            'is_at_closed_place' => 'Is At Closed Place',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'region_id' => 'Region ID',
        ];
    }
}
