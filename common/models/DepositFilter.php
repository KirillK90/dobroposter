<?php

namespace common\models;

use common\components\DepositsDataProvider;
use common\components\helpers\HDates;
use common\components\helpers\HStrings;
use common\enums\Currency;
use common\enums\DictionaryType;
use common\enums\RatePeriod;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class DepositFilter
 * @package backend\models
 */
class DepositFilter extends Model
{
    const SPECIAL_INCLUDE = 'include';
    const SPECIAL_EXCLUDE = 'exclude';

    const MAX_AMOUNT = 1000000000;

    public $isProf;

    public $ids;
    public $city_ids;
    public $bank_ids;
    public $bank_id;
    public $payment_periods;
    public $payment_period_ids;
    public $capitalization;
    public $capitalization_id;
    public $capitalization_period_ids;
    public $special_types;
    public $special_type_ids;
    public $exclude_special_type_ids;
    public $period;
    public $bank_top;
    public $currency;
    public $currencies;
    public $early_termination_method_id;
    public $has_capitalization;
    public $is_replenishment_possible;
    public $is_early_termination_possible;
    public $is_partial_withdrawal_possible;
    public $amount;

    public $available_days; //Сколько дней назад добавлен (не раньше)
    public $except_ids; // исключенные id

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function loadDefaults($withProf = false)
    {
        $this->currency = $this->currency ?: Currency::RUB;
        $this->amount = $this->amount ?: Currency::getDefaultAmount(Currency::RUB);
        $this->period = $this->period ?: RatePeriod::YEAR;
        if ($withProf) {
            $this->loadProfDefaults();
        }
    }

    public function loadProfDefaults()
    {
        $this->special_types = self::SPECIAL_EXCLUDE;
        $this->exclude_special_type_ids = [2, 3, 8];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['amount', 'filter', 'filter' => function($value){
                $value = preg_replace('/[^\d]/', '', $value);
                if ($value > self::MAX_AMOUNT) {
                    $value = self::MAX_AMOUNT;
                }
                return $value;
            }],
            [['amount'], 'integer', 'min' => 0, 'max' => self::MAX_AMOUNT],
            [['capitalization_id', 'amount', 'bank_top', 'early_termination_method_id', 'available_days'], 'integer'],
            [['ids', 'city_ids', 'bank_ids', 'payment_period_ids', 'capitalization_period_ids', 'special_type_ids',
                'exclude_special_type_ids'], 'each', 'rule' => ['integer']],
            [['currencies'], 'each', 'rule' => ['string', 'max' => 3]],
            [['currency'], 'string', 'max' => 3],
            [['is_replenishment_possible', 'is_early_termination_possible', 'is_partial_withdrawal_possible', 'has_capitalization'], 'boolean'],
            [['special_types', 'payment_periods', 'capitalization', 'isProf'], 'safe'],
            [['period'], 'checkPeriod'],
            [['period'], 'in', 'range' => RatePeriod::getValues()],
        ];
    }

    public function checkPeriod()
    {
        if (is_numeric($this->period)) {
            $this->period = RatePeriod::getValue($this->period);
        }
    }

    public function attributeLabels()
    {
        return array_merge((new Deposit())->attributeLabels(), [
            'city_ids' => "Регионы",
            'bank_ids' => "Банки",
            'currencies' => "Валюты",
            'period' => "Срок",
            'amount' => "Сумма",
            'available_days' => "Доступен не более"
        ]);
    }

    public function searchAdmin($filterModel)
    {
        $query = Deposit::find();
        $query->with('bank', 'params', 'rates', 'categories');
        $query->innerJoinWith(['ratings' => function(ActiveQuery $q) {
            $q->andFilterWhere(['deposit_rating.currency_id' => $this->currency]);
        }], true);
        $query->select(['deposit.*', 'max(deposit_rating.value) rating', 'max(rate.rate_max) max_rate']);
        $query->groupBy(['deposit.id']);
        $query->andFilterWhere([
            'deposit.id' => $filterModel->id,
        ]);
        $query->andFilterWhere(['deposit.id' => $this->ids]);
        $query->andFilterWhere(['ilike', 'deposit.product_name', $filterModel->product_name]);
        $query->andFilterWhere([
            'deposit.id' => $this->ids,
            'deposit.bank_id' => $this->bank_ids,
            'deposit.capitalization_id' => $this->capitalization_id,
            'deposit.is_replenishment_possible' => $this->is_replenishment_possible,
            'deposit.early_termination_method_id' => $this->early_termination_method_id,
            'deposit.is_partial_withdrawal_possible' => $this->is_partial_withdrawal_possible
        ]);

        if ($this->except_ids) {
            $query->andFilterWhere(['not', [
                'deposit.id' => $this->except_ids,
            ]]);
        }

        if ($this->available_days) {
            $query->andWhere(['>=', 'deposit.created_at_date', HDates::long("today -{$this->available_days} days")]);
        }

        if ($this->city_ids) {
            $query->innerJoin('deposit_regions', ['and', 'deposit_regions.deposit_id = deposit.id', ['deposit_regions.region_id' => $this->city_ids]]);
        }

        $query->innerJoinWith(['rates' => function (Query $query) {
            $query->from('deposit_rate rate');
            if ($this->period) {
                list($daysMin, $daysMax) = RatePeriod::toDaysPrecision($this->period);
                $query->andWhere(['or', 'rate.period_from is null', ['<=', 'rate.period_from', $daysMax]]);
                $query->andWhere(['or', 'rate.period_to is null', ['>=', 'rate.period_to', $daysMin]]);
            }
            if ($this->currencies) {
                $query->andWhere(['rate.currency_id' => $this->currencies]);
            }
            if ($this->currency) {
                $query->andWhere(['rate.currency_id' => $this->currency]);
            }
            if ($this->amount) {
                $query->andWhere(['or', 'rate.amount_from is null', ['<=', 'rate.amount_from', $this->amount]]);
                $query->andWhere(['or', 'rate.amount_to is null', ['>=', 'rate.amount_to', $this->amount]]);
            }
        }], false);

        if ($this->payment_period_ids) {
            $query->innerJoinWith(['params' => function (Query $query) {
                $query->from('deposit_params payment_period_ids');
                $query->where(['payment_period_ids.type' => DictionaryType::DepositPaymentPeriod, 'payment_period_ids.param_id' => $this->payment_period_ids]);
            }], false);
        }

        if ($this->special_type_ids) {
            $query->innerJoinWith(['params' => function (Query $query) {
                $query->from('deposit_params special_type_ids');
                $query->where(['special_type_ids.type' => DictionaryType::DepositSpecialType, 'special_type_ids.param_id' => $this->special_type_ids]);
            }], false);
        }

        if ($this->capitalization_period_ids) {
            $query->andWhere(['<>', 'capitalization_id', Dictionary::NO_CAPITALIZATION_VALUE]);
            $query->innerJoinWith(['params' => function (Query $query) {
                $query->from('deposit_params capitalization_period_ids');
                $query->where(['capitalization_period_ids.type' => DictionaryType::DepositCapitalizationPeriod, 'capitalization_period_ids.param_id' => $this->capitalization_period_ids]);
            }], false);
        }

        if ($this->is_early_termination_possible) {
            $query->innerJoinWith(['depositData' => function (Query $query) {
                $query->where(['deposit_data.is_early_termination_possible' => $this->is_early_termination_possible]);
            }], false);
        }

        if (in_array($this->bank_top, [20, 100])) {
            $bankIds = array_keys(Bank::getTopBanksListForProfFilter($this->bank_top));
            $query->andWhere(['deposit.bank_id' => $bankIds]);
        }

        return new DepositsDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['rating' => SORT_ASC],
                'attributes' => [
                    'max_rate' => [
                        'asc' => ['max_rate' => SORT_DESC],
                        'desc' => ['max_rate' => SORT_ASC],
                        'label' => 'По ставке',
                    ],
                    'rating' => [
                        'asc' => ['rating' => SORT_DESC],
                        'desc' => ['rating' => SORT_ASC],
                        'default' => SORT_DESC,
                        'label' => 'По рейтингу',
                    ],
                    'created_at_date' => [
                        'asc' => ['deposit.created_at_date' => SORT_ASC],
                        'desc' => ['deposit.created_at_date' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'По дате',
                    ]
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 50
            ]
        ]);
    }


    public function search($data = [], $pageSize = 10)
    {
        $query = $this->getQuery($data);

        return new DepositsDataProvider([
            'query' => $query,
            'sort' => [
//                'defaultOrder' => ['id' => SORT_ASC],
                'defaultOrder' => ['rating' => SORT_ASC],
                'attributes' => [
                    'max_rate' => [
                        'asc' => ['max_rate' => SORT_DESC],
                        'desc' => ['max_rate' => SORT_ASC],
                        'label' => 'По ставке',
                    ],
                    'rating' => [
                        'asc' => ['rating' => SORT_DESC],
                        'desc' => ['rating' => SORT_ASC],
                        'default' => SORT_DESC,
                        'label' => 'По рейтингу',
                    ]
                ]
            ],
            'pagination' => [
                'defaultPageSize' => $pageSize
            ]
        ]);
    }

    /**
     * @param $data
     * @return \yii\db\ActiveQuery
     */
    public function getQuery($data = [])
    {
        $model = new Deposit();
        $model->load($data);

        $query = Deposit::find();
        $query->with('bank', 'params', 'rates', 'alias');
        $query->innerJoinWith(['ratings' => function(ActiveQuery $q) {
            $q->andFilterWhere(['deposit_rating.currency_id' => $this->currency]);
        }], true);
        $query->select(['deposit.*', 'max(deposit_rating.value) as rating', 'max(rate.rate_max) max_rate']);
        $query->groupBy(['deposit.id']);

        $query->leftJoin('category_deposit cd', 'cd.deposit_id = deposit.id and cd.category_id =:bestId', [':bestId' => VKLADY_MESYACA_ID]);
        $query->addSelect('cd.custom_url as custom_url');
        $query->addGroupBy('cd.custom_url');

        $query->andFilterWhere([
            'deposit.id' => $model->id,
            'deposit.bank_id' => $this->bank_id,
        ]);
        $query->andFilterWhere(['deposit.id' => $this->ids]);
        $query->andFilterWhere(['ilike', 'deposit.product_name', $model->product_name]);

        if ($this->city_ids) {
            $query->innerJoin('deposit_regions', ['and', 'deposit_regions.deposit_id = deposit.id', ['deposit_regions.region_id' => $this->city_ids]]);
        }

        if ($this->available_days) {
            $query->andWhere(['>=', 'deposit.created_at_date', HDates::long("today -{$this->available_days} days")]);
        }

        $query->innerJoinWith(['rates' => function (Query $query) {
            $query->from('deposit_rate rate');
            if ($this->period) {
                list($daysMin, $daysMax) = RatePeriod::toDaysPrecision($this->period);
                $query->andWhere(['or', 'rate.period_from is null', ['<=', 'rate.period_from', $daysMax]]);
                $query->andWhere(['or', 'rate.period_to is null', ['>=', 'rate.period_to', $daysMin]]);
            }
            if ($this->currencies) {
                $query->andWhere(['rate.currency_id' => $this->currencies]);
            }
            if ($this->currency) {
                $query->andWhere(['rate.currency_id' => $this->currency]);
            }
            if ($this->amount) {
                $this->checkAmount();
                $query->andWhere(['or', 'rate.amount_from is null', ['<=', 'rate.amount_from', $this->amount]]);
                $query->andWhere(['or', 'rate.amount_to is null', ['>=', 'rate.amount_to', $this->amount]]);
            }
        }], false);


//        if ($this->isProf) {
        $this->applyProfyParams($query);
//        }

        return $query;
    }

    protected function applyProfyParams(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'deposit.id' => $this->ids,
            'deposit.capitalization_id' => $this->capitalization_id,
            'deposit.is_replenishment_possible' => $this->is_replenishment_possible,
            'deposit.early_termination_method_id' => $this->early_termination_method_id,
            'deposit.is_partial_withdrawal_possible' => $this->is_partial_withdrawal_possible
        ]);

        if ($this->payment_period_ids && $this->payment_periods) {
            $query->innerJoinWith(['params' => function (Query $query) {
                $query->from('deposit_params payment_period_ids');
                $query->where(['payment_period_ids.type' => DictionaryType::DepositPaymentPeriod, 'payment_period_ids.param_id' => $this->payment_period_ids]);
            }], false);
        }

        if ($this->special_type_ids && $this->special_types == self::SPECIAL_INCLUDE) {
            $query->innerJoinWith(['params' => function (Query $query) {
                $query->from('deposit_params special_type_ids');
                $query->where(['special_type_ids.type' => DictionaryType::DepositSpecialType, 'special_type_ids.param_id' => $this->special_type_ids]);
            }], false);
        } elseif ($this->exclude_special_type_ids && $this->special_types == self::SPECIAL_EXCLUDE) {
            $query->leftJoin('deposit_params special_type_ids', 'special_type_ids.deposit_id = deposit.id and special_type_ids.type = :specialType', [':specialType' => DictionaryType::DepositSpecialType]);
            $query->andWhere([
                    'or',
                    ['not',  ['special_type_ids.param_id' => $this->exclude_special_type_ids]],
                    ['special_type_ids.type' => null]
            ]);
        }

        if ($this->capitalization_period_ids && $this->capitalization) {
            $query->andWhere(['<>', 'capitalization_id', Dictionary::NO_CAPITALIZATION_VALUE]);
            $query->innerJoinWith(['params' => function (Query $query) {
                $query->from('deposit_params capitalization_period_ids');
                $query->where(['capitalization_period_ids.type' => DictionaryType::DepositCapitalizationPeriod, 'capitalization_period_ids.param_id' => $this->capitalization_period_ids]);
            }], false);
        } elseif ($this->capitalization === '0') {
            $query->andWhere(['capitalization_id' => Dictionary::NO_CAPITALIZATION_VALUE]);
        } elseif ($this->capitalization === '1') {
            $query->andWhere(['not', ['capitalization_id' => Dictionary::NO_CAPITALIZATION_VALUE]]);
        }

        if ($this->is_early_termination_possible) {
            $query->innerJoinWith(['depositData' => function (Query $query) {
                $query->where(['deposit_data.is_early_termination_possible' => $this->is_early_termination_possible]);
            }], false);
        }

        if (in_array($this->bank_top, [20, 100])) {
            $bankIds = array_keys(Bank::getTopBanksListForProfFilter($this->bank_top));
            $query->andWhere(['deposit.bank_id' => $bankIds]);
        } elseif ($this->bank_ids && $this->bank_top === "1") {
            $query->andWhere(['deposit.bank_id' => $this->bank_ids]);
        }
    }

    public function applyCategory(CatalogCategory $category)
    {
        $value = $category->value;
//        $this->isProf = true;
        switch($category->type) {
            case DictionaryType::REGION:
                $this->city_ids[] = $value;
                break;
            case DictionaryType::BANK:
                $this->bank_top = "1";
                $this->bank_ids[] = $value;
                break;
            case DictionaryType::DepositPaymentPeriod:
                $this->payment_periods = "1";
                $this->payment_period_ids[] = $value;
                break;
            case DictionaryType::DepositCapitalization:
                $this->capitalization_id[] = $value;
                break;
            case DictionaryType::DepositCapitalizationPeriod:
                $this->capitalization = "1";
                $this->capitalization_period_ids[] = $value;
                break;
            case DictionaryType::DepositSpecialType:
                $this->special_types = self::SPECIAL_INCLUDE;
                $this->special_type_ids[] = $value;
                break;
            case DictionaryType::RATE_PERIOD:
                $this->period = $value;
                break;
            case DictionaryType::CURRENCY:
                $this->currency = $value;
                break;
            case DictionaryType::REPLENISHMENT_POSSIBLE:
                $this->is_replenishment_possible = $value;
                break;
            case DictionaryType::PARTIAL_WITHDRAWAL_POSSIBLE:
                $this->is_partial_withdrawal_possible = $value;
                break;
            case DictionaryType::EARLY_TERMINATION_POSSIBLE:
                $this->is_early_termination_possible = $value;
                break;
            case DictionaryType::DepositEarlyTerminationMethod:
                $this->early_termination_method_id[] = $value;
                break;
            default:
                if ($category->dynamic) {
                    $this->attributes = FormData::getFilteredData($category->form_hash, self::className());
                    $this->loadDefaults();
                } else {
                    $this->ids = $category->getDepositIds() ?: [-1];
                }
        }
    }

    public function getValues($field)
    {
        $valuesMap = [
            'payment_period_ids' => Dictionary::getValues(DictionaryType::DepositPaymentPeriod),
            'capitalization_period_ids' => Dictionary::getValues(DictionaryType::DepositCapitalizationPeriod),
        ];
        return ArrayHelper::getValue($valuesMap, $field, []);
    }

    public function attributeHints()
    {
        return [
            'payment_periods' => 'Периодичность выплаты процентов определяется договором банковского вклада. Если вклад с капитализацией процентов, то чем чаще выплачиваются проценты и добавляются к сумме вклада, тем больше будет доход по вкладу',
            'capitalization' => 'Присоединение начисленных процентов по вкладу к основной сумме вклада. Позволяет в дальнейшем осуществлять начисление процентов на проценты, в результате чего общая доходность по депозиту возрастает.',
            'is_replenishment_possible' => 'Возможность пополнять вклад в течение срока действия договора по вкладу. Иногда в договоре прописываются ограничения по размерам взносов и общей сумме и сроках пополнения вклада.',
            'is_partial_withdrawal_possible' => 'Возможность снять часть вклада без потери процентов и расторжения договора по вкладу.',
            'is_early_termination_possible' => 'Наличие специальной льготной ставки при досрочном расторжении вклада, позволяет сохранить доход по депозиту.',
            'special_types' => 'Специальные вклады - это депозиты либо ориентированы, на более узкую группу лиц (Например: пенсионный - могут открыть только пенсионеры, детский - вклад открывается родителями в пользу своих детей), либо имеющие специальные условия (Например: мультивалютные - позволяет клиентам одновременно хранить деньги в различных валюта, страховой - открывается лицам заключившим договор накопительного страхования жизни).',
        ];
    }

    public function getAmountStr()
    {
        return HStrings::currency($this->amount);
    }

    /**
     * Клонирует значения фильтра и
     */
    public function searchBest()
    {
        $query = $this->getQuery();
        $query->andWhere('cd.custom_url is not null');
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['max_rate' => SORT_DESC],
                'attributes' => [
                    'max_rate' => [
                        'asc' => ['max_rate' => SORT_DESC],
                        'desc' => ['max_rate' => SORT_ASC],
                        'label' => 'По ставке',
                    ]
                ]
            ],
            'pagination' => false
        ]);
    }

    public function checkAmount()
    {
        if ($this->amount > self::MAX_AMOUNT) {
            $this->amount = self::MAX_AMOUNT;
        }
    }
}