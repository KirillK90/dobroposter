<?php

namespace common\models;

use common\components\helpers\SubstitutionsHelper;
use common\enums\SeoCustomParam;
use common\enums\SeoParam;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "seo".
 *
 * @property integer $id
 * @property string $h1
 * @property string $sub_title
 * @property string $breadcrumb
 * @property string $h2
 * @property string $text
 * @property string $text_geo
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 *
 * @property PageSeo $pageSeo
 */
class Seo extends \yii\db\ActiveRecord
{
    const EXAMPLE_REGION_ID = 4; // Москва
    const EXAMPLE_BANK_ID = 2764; //Газпромбанк
    const EXAMPLE_DEPOSIT_ID = 1; //Срочный
    const EXAMPLE_CATEGORY_ID = 586; //Пенсионные

    public $substitutions = [];

    public $page;
    public $param_id;
    /** @var  ActiveRecord */
    public $paramModel;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo';
    }

    /**
     * @param $param_id
     * @param $page
     * @return SubstitutionsInterface
     */
    public static function findParamModel($param_id, $page)
    {
        switch(SeoCustomParam::getParam($page)) {
            case SeoCustomParam::REGION_ID:
                return Region::findOne($param_id);
            case SeoCustomParam::BANK_ID:
                return Bank::findOne($param_id);
            case SeoCustomParam::CATEGORY_ID:
                return CatalogCategory::findOne($param_id);
            default:
                return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sub_title', 'text', 'text_geo', 'meta_title', 'meta_description', 'meta_keywords', 'h1', 'breadcrumb', 'h2'], 'trim'],
            [['sub_title', 'text', 'text_geo', 'meta_title', 'meta_description', 'meta_keywords'], 'string'],
            [['h1', 'breadcrumb', 'h2'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'breadcrumb' => 'Хлебные крошки (Последний пункт)',
            'h1' => 'H1',
            'sub_title' => 'Подстрочник',
            'h2' => 'H2',
            'text_geo' => 'Текст + город',
            'text' => 'Текст',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageSeo()
    {
        return $this->hasOne(PageSeo::className(), ['seo_id' => 'id']);
    }

    public function getSubstitution($token)
    {
        $token = trim($token, "{}");
        return ArrayHelper::getValue($this->substitutions, "{{$token}}", 'n/a');
    }

    public function setSubstitutions(array $substitutions)
    {
        $this->substitutions = array_merge($substitutions, $this->substitutions);
    }

    public function setExampleSubstitutions()
    {
        $this->setSubstitutions(SubstitutionsHelper::getCommonSubstitutions());

        /** @var Region $city */
        $city = Region::findOne(self::EXAMPLE_REGION_ID);
        $this->setSubstitutions(SubstitutionsHelper::getRegionSubstitutions($city, true));

        /** @var Bank $bank */
        $bank = Bank::findOne(self::EXAMPLE_BANK_ID);
        $this->setSubstitutions($bank->getSubstitutions());

        /** @var Deposit $deposit */
        $deposit = Deposit::findOne(self::EXAMPLE_DEPOSIT_ID);
        $this->setSubstitutions($deposit->getSubstitutions());

        /** @var CatalogCategory $category */
        $category = CatalogCategory::findOne(self::EXAMPLE_CATEGORY_ID);
        $this->setSubstitutions($category->getSubstitutions());
    }

    public function loadFromDefault(ActiveRecord $defaultSeo)
    {
        foreach(SeoParam::getValues() as $param)
        {
            $this->setAttribute($param, SeoParam::applySubstitutions($defaultSeo->getAttribute($param), $this->substitutions, false));
        }
    }

    public function setParamModel($page, $param_id)
    {
        $this->page = $page;
        $this->param_id = $param_id;
        $paramModel = Seo::findParamModel($param_id, $page);
        if (!$paramModel) {
            throw new NotFoundHttpException("Параметр не найден");
        }
        $this->setSubstitutions($paramModel->getSubstitutions());
        $this->paramModel = $paramModel;
    }
}
