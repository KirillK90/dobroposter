<?php

namespace common\models;

use common\enums\PageType;
use Yii;

/**
 * This is the model class for table "{{%page_banners}}".
 *
 * @property integer $id
 * @property string $page
 * @property string $place
 * @property integer $banner_place_id
 * @property boolean $default
 * @property integer $override_banner_id
 * @property boolean $enabled
 *
 * @property BannerPlace $bannerPlace [[getBannerPlace()]] [[setBannerPlace()]]
 * @property Banner $overrideBanner [[getOverrideBanner()]] [[getOverrideBanner()]]
 *
 * @property string $pageLabel [[getPageLabel()]]
 */
class PageBanners extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_banners}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['default', 'enabled'], 'default', 'value' => true],
            [['page', 'banner_place_id','place'], 'required'],
            ['page', 'in', 'range' => PageType::getValues()],
            ['banner_place_id', 'exist', 'targetClass' => BannerPlace::className(), 'targetAttribute' => 'id'],
            ['override_banner_id', 'exist', 'targetClass' => Banner::className(), 'targetAttribute' => 'id'],
            [['default', 'enabled'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['page', 'banner_place_id', 'default', 'override_banner_id', 'enabled', 'place'],
            self::SCENARIO_CREATE => ['page', '!banner_place_id', 'default', 'override_banner_id', 'enabled'],
            self::SCENARIO_UPDATE => ['!banner_place_id', 'default', 'override_banner_id', 'enabled'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page' => 'Страница',
            'banner_place_id' => 'Баннерное место',
            'default' => 'по умолчанию',
            'override_banner_id' => 'Переопределен',
            'enabled' => 'Включен',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBannerPlace()
    {
        return $this->hasOne(BannerPlace::className(), ['id' => 'banner_place_id']);
    }

    /**
     * @param BannerPlace $place
     */
    public function setBannerPlace(BannerPlace $place)
    {
        $this->banner_place_id = $place->id;
        $this->populateRelation('bannerPlace', $place);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOverrideBanner()
    {
        return $this->hasOne(Banner::className(), ['id' => 'override_banner_id']);
    }

    /**
     * @param Banner $banner
     */
    public function setOverrideBanner(Banner $banner)
    {
        $this->override_banner_id = $banner->id;
        $this->populateRelation('overrideBanner', $banner);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageLabel()
    {
        return PageType::getName($this->page);
    }
}
