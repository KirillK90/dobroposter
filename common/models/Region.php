<?php

namespace common\models;

use common\components\helpers\HStrings;
use common\components\helpers\SubstitutionsHelper;
use common\enums\EntityType;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "region".
 *
 * @property integer $id
 * @property boolean $is_active
 * @property string $name
 * @property string $name_genitive
 * @property string $name_prepositional
 * @property integer $parent_id
 * @property boolean $is_city
 * @property string $emblem_url
 * @property string $region_url
 * @property integer $sort_number
 * @property integer $zoom
 * @property string $type
 * @property boolean $is_regional_center
 * @property boolean $is_in_top
 * @property double $latitude
 * @property double $longitude
 * @property boolean $ready
 *
 * relations
 * @property Region[] $children
 * @property Region $parent
 * @property TopCity $top
 * @property Alias $alias
 * @property string $slug
 */
class Region extends \yii\db\ActiveRecord
{

    const RUSSIA = 'russia';
    const RUSSIA_NAME = 'Вся Россия';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    public static function getNotTopCities()
    {
        $query = self::find()
            ->where(['is_city' => true, 'is_regional_center' => true])
            ->joinWith('top')
            ->andWhere(['top_city.id' => null]);


        return $query->orderBy('region.name')->all();
    }

    /**
     * @param array $exceptIds
     * @return Region[]
     */
    public static function getCities($exceptIds = array())
    {
        $query = self::find()
            ->where(['is_city' => true])
            ->andWhere(['is_regional_center' => true]);
        if ($exceptIds) {
            $query->andWhere(['not in', 'id', $exceptIds]);
        }

        return $query->orderBy('region.name')->all();
    }

    /**
     * @return Region[]
     */
    public static function getTopCities()
    {
        return Region::find()->innerJoinWith('top')->orderBy('top_city.id')->all();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'name', 'is_city', 'sort_number', 'type', 'is_regional_center', 'is_in_top'], 'required'],
            [['is_active', 'is_city', 'is_regional_center', 'is_in_top'], 'boolean'],
            [['parent_id', 'sort_number', 'zoom'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['name', 'name_genitive', 'name_prepositional', 'emblem_url', 'region_url', 'type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_active' => 'Is Active',
            'name' => 'Name',
            'name_genitive' => 'Name genitive',
            'name_prepositional' => 'Name Prepositional',
            'parent_id' => 'Parent ID',
            'is_city' => 'Is City',
            'emblem_url' => 'Emblem Url',
            'region_url' => 'Region Url',
            'sort_number' => 'Sort Number',
            'zoom' => 'Zoom',
            'type' => 'Type',
            'is_regional_center' => 'Is Regional Center',
            'is_in_top' => 'Is In Top',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    public function getSlug()
    {
        return ArrayHelper::getValue($this->alias, 'slug', HStrings::transliterate($this->name));
    }

    public function getAlias()
    {
        return $this->hasOne(Alias::className(), ['entity_id' => 'id'])->where(['alias.entity_type' => EntityType::REGION])->orderBy(["created_at" => SORT_DESC]);
    }

    public function getChildren()
    {
        return $this->hasMany(Region::className(), ['parent_id' => 'id']);
    }

    public function getParent()
    {
        return $this->hasOne(Region::className(), ['id' => 'parent_id']);
    }

    public function getTop()
    {
        return $this->hasOne(TopCity::className(), ['region_id' => 'id']);
    }

    public function getSubstitutions()
    {
        return SubstitutionsHelper::getRegionSubstitutions($this);
    }

    public function getFullName()
    {
        $name = $this->name;
        $parent = $this->parent;
        while ($parent && ($parent->type != 'main' || !$parent->is_active)) $parent = $parent->parent;
        if ($parent) {
            $name .= " ({$parent->name})";
        }
        return $name;
    }
}
