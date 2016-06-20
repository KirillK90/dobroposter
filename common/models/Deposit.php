<?php

namespace common\models;

use common\components\helpers\HStrings;
use common\enums\Currency;
use common\enums\DictionaryType;
use common\enums\EntityType;
use frontend\models\RegionUrlInterface;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "deposit".
 *
 * @property integer $id
 * @property boolean $is_active
 * @property string $product_name
 * @property integer $bank_id
 * @property string $created_at_date
 * @property string $updated_at_date
 * @property string $positive
 * @property string $positive_html
 * @property string $negative
 * @property string $negative_html
 * @property string $special_restrictions
 * @property string $special_restrictions_html
 * @property string $product_additional_information
 * @property integer $capitalization_id
 * @property string $capitalization_comment
 * @property integer $early_termination_method_id
 * @property string $early_termination_comment
 * @property integer $minimum_balance_id
 * @property double $minimum_balance_percent
 * @property boolean $is_online_opening_possible
 * @property boolean $is_remote_opening_possible
 * @property string $remote_opening_comment
 * @property boolean $is_partial_withdrawal_possible
 * @property string $partial_withdrawal_comment
 * @property string $payment_comment
 * @property boolean $is_rate_increase_possible
 * @property string $rate_increase_comment
 * @property boolean $is_replenishment_possible
 * @property string $replenishment_comment
 * @property string $special_type_comment
 * @property boolean $is_prolongation_possible
 * @property integer $prolongation_max
 * @property string $prolongation_comment
 * @property string $rate_comment
 *
 * relations
 * @property Bank $bank
 * @property DepositRate[] $rates
 * @property DepositParams[] $params
 * @property Region[] $regions
 * @property DepositRegions[] $depositRegions
 * @property DepositData $depositData
 * @property array $substitutions
 * @property DepositRating[] ratings
 * @property Alias $alias
 * @property string $slug
 * @property CatalogCategory[] $categories
 * @property CategoryDeposit[] $categoryDeposits
 */
class Deposit extends \yii\db\ActiveRecord implements RegionUrlInterface
{
    public $htmlParams = null;

    /** @var null Кастомная ссылка для подборки */
    public $custom_url = null;

    public $deleted = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deposit';
    }

    public static function getBest($currency = Currency::RUB, $limit = 3)
    {
        /** @var Deposit[] $deposits */
        $deposits = self::getDb()->cache(function() use($currency) {
            return self::find()
                ->with('bank')
                ->innerJoinWith('rates')
                ->innerJoinWith('ratings')
                ->where(['deposit_rate.currency_id' => $currency, 'deposit_rating.currency_id' => $currency])
                ->orderBy('deposit_rating.value DESC')
                ->groupBy(['deposit.id', 'deposit_rating.value'])
                ->limit(20)->all();
        }, 3600, ApiLog::getDependency());

        shuffle($deposits);
        return array_slice($deposits, 0, $limit);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'product_name', 'bank_id', 'created_at_date'], 'required'],
            [['is_active', 'is_online_opening_possible', 'is_remote_opening_possible', 'is_partial_withdrawal_possible', 'is_rate_increase_possible', 'is_replenishment_possible', 'is_prolongation_possible'], 'boolean'],
            [['bank_id', 'capitalization_id', 'early_termination_method_id', 'minimum_balance_id', 'prolongation_max'], 'integer'],
            [['created_at_date', 'updated_at_date'], 'safe'],
            [['special_type_comment',  'remote_opening_comment', 'positive', 'positive_html', 'negative', 'negative_html', 'special_restrictions', 'special_restrictions_html', 'product_additional_information', 'capitalization_comment', 'early_termination_comment', 'partial_withdrawal_comment', 'payment_comment', 'rate_increase_comment', 'replenishment_comment', 'prolongation_comment', 'rate_comment'], 'safe'],
            [['minimum_balance_percent'], 'number'],
            [['product_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'rating' => 'Рейтинг',
            'max_rate' => 'Ставка',
            'bank_id' => 'ID банка',
            'bank.name' => 'Банк',
            'bank.logo_url' => 'Лого банка',

            'created_at_date' => 'Добавлен',
            'updated_at_date' => 'Обновлен',

            'is_active' => 'Активен',
            'regionNames' => 'Регионы, в которых доступен вклад',

            'product_additional_information' => 'Дополнительная информация о продукте',
            'positive' => 'Плюсы',
            'positive_html' => 'Плюсы',
            'negative' => 'Минусы',
            'negative_html' => 'Минусы',

            'minimum_balance_id' => 'Тип минимального неснижаемого остатка',
            'paramsHtml.minimum_balance_id' => 'Тип минимального неснижаемого остатка',
            'minimum_balance_percent' => 'Процент минимального неснижаемого остатка',

            'is_partial_withdrawal_possible' => 'Возможно ли чаcтичноe снятие',
            'paramsHtml.partial_withdrawal_restriction_ids' => 'Ограничения частичного снятия',
            'paramsHtml.is_partial_withdrawal_possible' => 'Возможно ли чаcтичноe снятие',
            'partial_withdrawal_comment' => 'Пояснения к частичному снятию',

            'payment_comment' => 'Примечания к выплате процентов',
            'paramsHtml.payment_method_ids' => 'Способ выплат процентов',
            'paramsHtml.payment_period_ids' => 'Периодичность выплат процентов',
            'payment_period_ids' => 'Выплата процентов',

            'is_replenishment_possible' => 'Возможно ли пополнение',
            'paramsHtml.replenishment_restrictions_id' => 'Ограничения для пополнения',
            'replenishment_comment' => 'Пояснения к пополнению',

            'paramsHtml.special_type_ids' => 'Специальные типы вклада',
            'special_type_ids' => 'Специальные типы вклада',
            'special_type_comment' => 'Пояснение к типу специального вклада',

            'is_prolongation_possible' => 'Возможна ли автопролонгация',
            'prolongation_max' => 'Допустимое количество пролонгаций',
            'prolongation_comment' => 'Пояснение к автопролонгации',

            'rate_comment' => 'Примечание к ставками',

            'capitalization_id' => 'Капитализации',
            'paramsHtml.capitalization_id' => 'Капитализация',
            'capitalization_comment' => 'Примечание к капитализации',
            'paramsHtml.capitalization_period_ids' => 'Периодичность капитализации',
            'capitalization_period_ids' => 'Периодичность капитализации',

            'special_restrictions_html' => 'Специальные ограничения',
            'special_restrictions' => 'Специальные ограничения',

            'paramsHtml.early_termination_method_id' => 'Cпособ досрочного расторжения',
            'early_termination_method_id' => 'Cпособ досрочного расторжения',
            'early_termination_comment' => 'Условия досрочного расторжения',
            'is_early_termination_possible' => 'Возможно ли досрочное расторжение',

            'is_rate_increase_possible' => 'Возможно ли увеличение ставки',
            'paramsHtml.rate_increase_type_ids' => 'Типы увеличения ставки',
            'rate_increase_comment' => 'Пояснение к увеличению ставки',

            'is_online_opening_possible' => 'Возможно ли открытие вклада онлайн',
            'paramsHtml.online_opening_method_ids' => 'Способы открытия вклада онлайн',

            'is_remote_opening_possible' => 'Возможно ли удаленное открытие вклада',
            'remote_opening_comment' => 'Пояснение к удаленному открытию',

            'product_name' => 'Название продукта',

            'currencies' => 'Валюты',
            'periods' => 'Срок',
            'params' => 'Параметры',
        ];
    }

    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['id' => 'bank_id']);
    }

    public function getRates()
    {
        return $this->hasMany(DepositRate::className(), ['deposit_id' => 'id']);
    }

    public function getParams()
    {
        return $this->hasMany(DepositParams::className(), ['deposit_id' => 'id']);
    }

    public function getDepositData()
    {
        return $this->hasOne(DepositData::className(), ['id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Region::className(), ['id' => 'region_id'])
            ->viaTable(DepositRegions::tableName(), ['deposit_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(CatalogCategory::className(), ['id' => 'category_id'])
            ->via("categoryDeposits")->indexBy('id');
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryDeposits()
    {
        return $this->hasMany(CategoryDeposit::className(), ['deposit_id' => 'id'])->indexBy('category_id');
    }

    /**
     * @return ActiveQuery
     */
    public function getDepositRegions()
    {
        return $this->hasMany(DepositRegions::className(), ['deposit_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRatings()
    {
        return $this->hasMany(DepositRating::className(), ['deposit_id' => 'id'])->indexBy('currency_id');
    }

    public function getSlug($fromDb = false)
    {
        return $fromDb || isset($this->relatedRecords['alias']) ? $this->alias->slug : HStrings::transliterate($this->product_name);
    }

    public function getAlias()
    {
        return $this->hasOne(Alias::className(), ['entity_id' => 'id'])->where(['alias.entity_type' => EntityType::DEPOSIT])->orderBy(["alias.created_at" => SORT_DESC]);
    }

    /**
     * Возвращает массив множественных полей типа "Периодичность выплат процентов" депозита в списке DetailView
     * @return array
     */
    public function getParamsHtml()
    {
        if (is_null($this->htmlParams)) {
            $this->refreshHtmlParams();
        }
        return $this->htmlParams;
    }

    /**
     * Формирует массив из множественных параметров для отображаения в списке DetailView
     * @return array|null
     */
    public function refreshHtmlParams()
    {
        $paramsArr = $htmlParams = [];

        foreach($this->params as $param) {
            $paramsArr[$param->type][] = $param->getValue();
        }
        foreach($paramsArr as $type => $values) {
            $htmlParams[DictionaryType::getAttribute($type)] = implode(", ", $values);
        }

        foreach(DictionaryType::$depositSingleAttributes as $attribute => $type) {
            $htmlParams[$attribute] = Dictionary::getValue($type, $this->$attribute);
        }
        $this->htmlParams = $htmlParams;
        return $this->htmlParams;
    }

    public function getRegionNames()
    {
        $names = [];
        foreach($this->regions as $region) {
            $names[$region->id] = $region->name;
        }
        return implode(", ", $names);
    }

    public function getAdminUrl()
    {
        return Url::to(['/deposits/update', 'id' => $this->id]);
    }

    public function getUrl($regionSlug = null, $currency = null, $period = null, $amount = null)
    {
        return Url::to(['/deposits/view', 'bankSlug' => $this->bank->getGenitiveSlug(), 'slug' => $this->getSlug(), 'regionSlug' => $regionSlug, 'curr' => $currency, 'period' => $period, 'amount' => $amount]);
    }

    public function getCurrencies()
    {
        $currencies = [];
        foreach($this->rates as $rate) {
            $currencies[$rate->currency_id] = $rate->currency_id;
        }

        return Currency::filterAndSort($currencies);
    }

    public function getCurrenciesStr()
    {
        $currencies = [];
        foreach($this->getCurrencies() as $currency) {
            $currencies[] = Html::tag('span', Currency::getSymbol($currency), ['class' => Currency::getAdminClass($currency)]);
        }
        return implode(', ', $currencies);
    }

    /**
     * @param $currency
     * @return DepositRate[]
     */
    public function getCurrencyRates($currency)
    {
        $rates = [];
        foreach ($this->rates as $rate) {
            if ($rate->currency_id == $currency) {
                $rates[] = $rate;
            }
        }
        return $rates;
    }

    public function getSubstitutions()
    {
        return [
            '{deposit:name}' => $this->product_name,
            '{bank:name}' => $this->bank->name,
            '{bank:name_genitive}' => $this->bank->getNameGenitive(),
        ];
    }

    /**
     * @param $currency
     * @return DepositCurrency
     */
    public function getDepositCurrency($currency)
    {
        $rates = $this->getCurrencyRates($currency);
        if (!$rates) return false;
        return new DepositCurrency([
            'deposit' => $this,
            'currency' => $currency,
            'rates' => $rates,
            'rating' => ArrayHelper::getValue($this->ratings, "$currency.value", 0),
        ]);
    }

    public function getParam($param, $default = 'нет')
    {
        $htmlParams = $this->getParamsHtml();
        if (isset($htmlParams[$param])) {
            return $htmlParams[$param];
        }
        if ($this->hasAttribute($param)) {
            return $this->$param ? $this->$param : $default;
        }
        return $default;
    }

    public function getSortLabel($name)
    {
        $sortLabels = $this->sortLabels();
        if (isset($sortLabels[$name])) {
            return $sortLabels[$name];
        }
        return $this->getAttributeLabel($name);
    }

    public function sortLabels()
    {
        return [
            'rating' => 'По рейтингу',
            'max_rate' => 'По ставке',
        ];
    }

    public function attributeHints()
    {
        return [
            'raiting' => 'Рейтинг вклада разработан для того, чтобы помочь вам найти лучший вклад с первого взгляда. Формула рейтинга учитывает множество параметров вклада и банка, в котором предполагается открыть вклад. В реальном времени сравнивается его доходность с другими предложениями. Каждому вкладу начисляются баллы от 1 до 100. Вложения во вклады с наибольшим количеством баллов, более выгодные и более надежные.'
        ];
    }

    public function getParamsStr()
    {
        $strParts = [];
        if ($this->capitalization_id == Dictionary::NO_CAPITALIZATION_VALUE) {
            $strParts[] = 'без капитализации';
        } else {
            $strParts[] = "капитализация ".$this->getParam('capitalization_period_ids');
        }
        $strParts[] = "выплата процентов ".$this->getParam('payment_period_ids');
        if ($this->is_replenishment_possible) {
            $strParts[] = "есть пополнение";
        } else {
            $strParts[] = "нет пополнения";
        }
        if ($this->is_partial_withdrawal_possible) {
            $strParts[] = "есть частичное снятие";
        } else {
            $strParts[] = "нет частичного снятия";
        }
        if ($this->early_termination_method_id == 2) {
            $strParts[] = "досрочное расторжение ".Dictionary::getValue(DictionaryType::DepositEarlyTerminationMethod, $this->early_termination_method_id);
        }
        if ($specialTypes = $this->getParam('special_type_ids', null)) {
            $strParts[] = $specialTypes;
        }
        return implode(', ', $strParts);
    }

    public function getCapitalization($default = "отсутствует")
    {
        if ($this->capitalization_id == Dictionary::NO_CAPITALIZATION_VALUE) {
            return $default;
        } else {
            return $this->getParam('capitalization_period_ids');
        }
    }

    public function getPaymentPeriod()
    {
        return $this->getParam('payment_period_ids');
    }

    public function getSpecialTypeIds()
    {
        $ids = [];
        foreach($this->params as $param) {
            if ($param->type == DictionaryType::DepositSpecialType) {
                $ids[] = $param->param_id;
            }
        }
        return $ids;
    }

    public function getMaxRating()
    {
        $ratings = [];
        foreach($this->ratings as $rating) {
            $ratings[] = $rating->value;
        }
        return max($ratings);
    }

    public function getMaxRate()
    {
        $maxRate = 0;
        $rates = $this->rates;
        foreach ($rates as $rate) {
            if ($rate->rate_max > $maxRate) {
                $maxRate = $rate->rate_max;
            }
        }
        return $maxRate;
    }

    public function getSimilar($currency)
    {
        $depositCurrency = $this->getDepositCurrency($currency);
        /** @var Deposit[] $deposits */
        $deposits = self::getDb()->cache(function() use($depositCurrency, $currency) {
            return self::find()
                    ->with('bank')
                    ->innerJoinWith('rates')
                    ->innerJoinWith('ratings')
                    ->where(['deposit_rate.currency_id' => $currency, 'deposit_rating.currency_id' => $currency])
                    ->andWhere(['not', ['deposit.id' => $this->id]])
                    ->orderBy('abs(deposit_rating.value - :rating) ASC, abs(max(deposit_rate.rate_max) - :max_rate) ASC')
                    ->groupBy(['deposit.id', 'deposit_rating.value'])
                    ->params(['rating' => $depositCurrency->rating, 'max_rate' => $depositCurrency->getMaxRate()])
                    ->limit(10)->all();
        }, 3600, ApiLog::getDependency());


        shuffle($deposits);
        return array_slice($deposits, 0, 3);
    }

    /**
     * @param Region $region
     * @return bool
     */
    public function hasRegion($region)
    {
        if (!$region) return true;
        return DepositRegions::find()->where(['deposit_id' => $this->id, 'region_id' => $region->id])->count();
    }

    public function getUpdateLink()
    {
        return Yii::getAlias('@admin/deposits/update/') . $this->id;
    }

    public function updateCustomUrl()
    {
        $this->custom_url = CategoryDeposit::find()->where(['deposit_id' => $this->id, 'category_id' => VKLADY_MESYACA_ID])->select('custom_url')->asArray()->scalar();
    }

}
