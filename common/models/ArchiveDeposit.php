<?php

namespace common\models;

use common\enums\Currency;
use common\enums\DictionaryType;
use frontend\models\RegionUrlInterface;
use Yii;

/**
 * This is the model class for table "deposit".
 *
 * @property integer $id
 * @property integer $_id
 * @property integer $_date
 * @property boolean $is_active
 * @property string $product_name
 * @property integer $bank_id
 * @property string $created_at_date
 * @property string $updated_at_date
 * @property string $special_restrictions
 * @property integer $capitalization_id
 * @property integer $early_termination_method_id
 * @property integer $minimum_balance_id
 * @property double $minimum_balance_percent
 * @property boolean $is_online_opening_possible
 * @property boolean $is_remote_opening_possible
 * @property boolean $is_partial_withdrawal_possible
 * @property boolean $is_rate_increase_possible
 * @property boolean $is_replenishment_possible
 * @property boolean $is_prolongation_possible
 * @property integer $prolongation_max
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
class ArchiveDeposit extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'archive_deposit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','_id','_date', 'is_active', 'product_name', 'bank_id', 'created_at_date'], 'required'],
            [['is_active', 'is_online_opening_possible', 'is_remote_opening_possible', 'is_partial_withdrawal_possible', 'is_rate_increase_possible', 'is_replenishment_possible', 'is_prolongation_possible'], 'boolean'],
            [['bank_id', 'capitalization_id', 'early_termination_method_id', 'minimum_balance_id', 'prolongation_max'], 'integer'],
            [['created_at_date', 'updated_at_date'], 'safe'],
            [['special_restrictions'], 'safe'],
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


            'created_at_date' => 'Добавлен',
            'updated_at_date' => 'Обновлен',

            'capitalization_id' => 'Капитализации',
            'early_termination_method_id' => 'Cпособ досрочного расторжения',
            'minimum_balance_id' => 'Тип минимального неснижаемого остатка',
            'minimum_balance_percent' => 'Процент минимального неснижаемого остатка',
            'is_online_opening_possible' => 'Возможно ли открытие вклада онлайн',
            'is_remote_opening_possible' => 'Возможно ли удаленное открытие вклада',
            'is_partial_withdrawal_possible' => 'Возможно ли чаcтичноe снятие',
            'is_rate_increase_possible' => 'Возможно ли увеличение ставки',
            'is_replenishment_possible' => 'Возможно ли пополнение',
            'is_prolongation_possible' => 'Возможна ли автопролонгация',
            'prolongation_max' => 'Допустимое количество пролонгаций',
            'special_restrictions' => 'Специальные ограничения',

            'is_active' => 'Активен',

            'product_name' => 'Название продукта',
            'bank_id' => 'ID банка',

        ];
    }

}
