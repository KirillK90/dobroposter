<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category_slide".
 *
 * @property integer $category_id
 * @property integer $deposit_id
 *
 * @property CatalogCategory $category
 */
class CategorySlide extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_slide';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'deposit_id'], 'required'],
            [['category_id', 'deposit_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'Category ID',
            'deposit_id' => 'Deposit ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CatalogCategory::className(), ['id' => 'category_id']);
    }
}
