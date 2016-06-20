<?php

namespace common\models;

use common\enums\PagePlace;
use Yii;

/**
 * This is the model class for table "{{%banner_place}}".
 *
 * @property integer $id
 * @property integer $default_banner_id
 * @property string $place
 *
 * @property Banner $defaultBanner
 * @property Banner[] $defaultBannerCandidates
 *
 * @property string $placeLabel [[getPlaceLabel()]]
 */
class BannerPlace extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'banner_place';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['default_banner_id', 'place'], 'required'],
            ['default_banner_id', 'exist', 'targetClass' => Banner::className(), 'targetAttribute' => 'id'],
            ['place', 'in', 'range' => PagePlace::getValues()],
            ['place', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['default_banner_id', 'place'],
            self::SCENARIO_CREATE => ['default_banner_id', 'place'],
            self::SCENARIO_UPDATE => ['default_banner_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'default_banner_id' => 'Баннер по умолчанию',
            'place' => 'Место',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultBanner()
    {
        return $this->hasOne(Banner::className(), ['id' => 'default_banner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultBannerCandidates()
    {
        return $this->hasMany(Banner::className(), ['place' => 'place']);
    }

    /**
     * @return string
     */
    public function getPlaceLabel()
    {
        return PagePlace::getName($this->place);
    }
}