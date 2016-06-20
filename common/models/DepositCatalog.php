<?php

namespace common\models;

use common\enums\CatalogType;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "deposit_catalog".
 *
 * @property integer $id
 * @property string $type
 * @property string $name
 * @property CatalogCategory[] $categories
 */
class DepositCatalog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deposit_catalog';
    }

    public function isPopular()
    {
        return $this->type == CatalogType::POPULAR_CATEGORIES;
    }

    public static function getCustomList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->where(['type' => CatalogType::CUSTOM])->column();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['name'], 'string'],
            [['type'], 'string', 'max' => 255]
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
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(CatalogCategory::className(), ['catalog_id' => 'id'])->orderBy('order');
    }

    /**
     * @param null $limit
     * @return CatalogCategory[]
     */
    public function getPopularCategories($limit = null)
    {
        return CatalogCategory::find()->where(['is_popular' => true, 'enabled' => true])->limit($limit)->all();
    }

    public function getCategoriesLinks()
    {
        $links = [];
        foreach ($this->categories as $category) {
            $links[] = $category->getViewLink();
        }

        return $links;
    }

}
