<?php

namespace common\models;

use common\enums\SeoCustomParam;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "page_seo".
 *
 * @property integer $id
 * @property string $page
 * @property integer $seo_id
 * @property integer $param_id
 * @property string $param_name
 *
 * @property Seo $seo
 * @property SubstitutionsInterface $param
 */
class PageSeo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page_seo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page', 'seo_id'], 'required'],
            [['param_id'], 'integer'],
            [['page', 'param_id', 'param_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page' => 'Page',
            'seo_id' => 'Seo ID',
            'param_id' => 'City ID',
            'param_name' => 'Param name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeo()
    {
        return $this->hasOne(Seo::className(), ['id' => 'seo_id']);
    }

    public function getParam()
    {
        switch($this->param_name) {
            case SeoCustomParam::REGION_ID:
                return $this->hasOne(Region::className(), ['id' => 'param_id']);
            case SeoCustomParam::BANK_ID:
                return $this->hasOne(Bank::className(), ['id' => 'param_id']);
            case SeoCustomParam::CATEGORY_ID:
                return $this->hasOne(CatalogCategory::className(), ['id' => 'param_id']);
            default:
                return null;
        }
    }

    public function getUpdateUrl()
    {
        if ($this->param_id) {
            return Url::to(["/seo/update", 'page' => $this->page, 'param_id' => $this->param_id]);
        } else {
            return Url::to(["/seo/update-default", 'page' => $this->page]);
        }
    }

    public function getDeleteUrl()
    {
        return Url::to(["/seo/delete", 'page' => $this->page, 'param_id' => $this->param_id]);
    }
}
