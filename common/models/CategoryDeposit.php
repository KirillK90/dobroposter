<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category_deposit".
 *
 * @property integer $deposit_id
 * @property integer $category_id
 * @property string $custom_url
 *
 * @property CatalogCategory $category
 */
class CategoryDeposit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_deposit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deposit_id', 'category_id'], 'required'],
            [['deposit_id', 'category_id'], 'integer'],
            [['custom_url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'deposit_id' => 'Deposit ID',
            'category_id' => 'Category ID',
            'custom_url' => 'Ссылка',
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
