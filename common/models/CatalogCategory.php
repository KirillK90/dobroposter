<?php

namespace common\models;

use common\components\helpers\HStrings;
use common\components\helpers\SubstitutionsHelper;
use common\enums\CatalogType;
use common\enums\DictionaryType;
use frontend\models\RegionUrlInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "catalog_category".
 *
 * @property integer $id
 * @property integer $catalog_id
 * @property integer $name
 * @property string $type
 * @property string $value
 * @property integer $order
 * @property boolean $is_popular
 * @property string $form_hash
 * @property string $slider_title
 * @property string $slug
 * @property boolean $enabled
 * @property boolean $dynamic
 *
 * @property DepositCatalog $catalog
 * @property CategoryDeposit[] $categoryDeposits
 * @property integer $maxOrder
 * @property string viewLink
 */
class CatalogCategory extends \yii\db\ActiveRecord implements RegionUrlInterface, SubstitutionsInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_category';
    }

    public static function getGroupedList()
    {
        return ArrayHelper::map(self::find()->innerJoinWith('catalog', false)->select(['catalog_category.id', 'catalog_category.name', 'deposit_catalog.name as catalog_name'])->asArray()->all(), 'id', 'name', 'catalog_name');
    }

    /**
     * @return CatalogCategory[]
     */
    public static function getCustomList()
    {
        return self::find()->innerJoinWith('catalog')->where(['deposit_catalog.type' => CatalogType::CUSTOM])->indexBy('id')->all();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['catalog_id', 'name'], 'required'],
            [['catalog_id'], 'integer'],
            [['is_popular'], 'boolean'],
            [['type', 'value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'catalog_id' => 'Catalog ID',
            'name' => 'Категория',
            'type' => 'Type',
            'value' => 'Value',
        ];
    }


    public function beforeSave($insert)
    {
        if (!$this->order) {
            $this->order = $this->maxOrder + 1;
        }
        if (!$this->slug) {
            $this->slug = HStrings::transliterate($this->name);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalog()
    {
        return $this->hasOne(DepositCatalog::className(), ['id' => 'catalog_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaxOrder()
    {
        return $this->hasMany(CatalogCategory::className(), ['catalog_id' => 'catalog_id'])->select('max("order")')->scalar();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryDeposits()
    {
        return $this->hasMany(CategoryDeposit::className(), ['category_id' => 'id'])->indexBy('deposit_id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepositIds()
    {
        $ids = [];
        foreach ($this->categoryDeposits as $categoryDeposit) {
            $ids[] = $categoryDeposit->deposit_id;
        }

        return $ids;
    }


    public function getViewLink()
    {
        return Html::a($this->name, $this->getViewUrl(), ['class' => $this->enabled ? 'category-link' : 'category-link dis']);
    }

    public function getViewUrl()
    {
        return Url::to(['/structure/category/', 'id' => $this->id]);
    }

    public function getUpdateUrl()
    {
        return Url::to(['/structure/update-category/', 'id' => $this->id]);
    }

    public function getUrl($region = null, $absolute = false)
    {
        switch($this->type) {
            case DictionaryType::BANK: return Url::to(['/banks/view', 'slug' => $this->slug, 'regionSlug' => $region], $absolute);
            case DictionaryType::REGION: return Url::to(['/deposits/search', 'regionSlug' => $this->slug], $absolute);
            default: return Url::to(['/catalogs/category', 'slug' => $this->slug, 'regionSlug' => $region], $absolute);
        }
    }

    public function getBankUrl(Bank $bank = null, $region = null, $forBlock = false)
    {
        if (!$bank) return $this->getUrl($region);
        if ($this->type == DictionaryType::REGION && !$forBlock) {
            return Url::to(['/banks/view', 'regionSlug' => $this->slug, 'slug' => $bank->getSlug()]);
        }
        if ($this->type != DictionaryType::BANK && $this->type != DictionaryType::REGION) {
            return Url::to(['/banks/category', 'categorySlug' => $this->slug, 'slug' => $bank->getSlug()]);
        }

        return $this->getUrl($region);
    }


    public function getSubstitutions()
    {
        return SubstitutionsHelper::getCategorySubstitutions($this);
    }

    public function getBankSubstitutions()
    {
        return [
            '{category:name}' => $this->name,
            '{category:catalog_name}' => $this->catalog->name,
        ];
    }

}
