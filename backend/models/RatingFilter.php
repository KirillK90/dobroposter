<?php

namespace backend\models;

use common\models\DepositRating;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class ArticleFilter
 * @package backend\models
 */
class RatingFilter extends DepositRating
{
    public $depositName;
    public $bankName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deposit_id', 'currency_id', 'value'], 'required'],
            [['deposit_id', 'value'], 'integer'],
            [['depositName', 'bankName'], 'string'],
            [['max_rate'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'depositName' => 'Вклад',
            'bankName' => 'Банк',
            'value' => 'Значение',
            'rating' => 'Рейтинг',
            'currency_id' => 'Валюта',
            'details' => 'Детализация',
            'max_rate' => 'Макс. ставка',
            'folk_rating' => 'Народный рейтинг',
        ];
    }


    public function search()
    {
        $query = self::find();
        $query->joinWith('deposit');
        $query->joinWith('bank.folkRating');
        $query->with('rates', 'params');
        $query->andFilterWhere([
            'deposit_rating.currency_id' => $this->currency_id,
        ]);
        if ($this->depositName) {
            $query->andWhere(['ilike', 'deposit.product_name', $this->depositName]);
        }
        if ($this->bankName) {
            $query->andWhere(['like', 'bank.name', $this->bankName]);
        }
        $query->joinWith('rates', false);
        $query->select(['deposit_rating.*', 'folk_rating.value as folk_rating', 'max(deposit_rate.rate_max) max_rate']);
        $query->groupBy(['deposit.id', 'deposit_rating.deposit_id', 'deposit_rating.currency_id', 'folk_rating.value']);
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'rating' => SORT_DESC,
                ],
                'attributes' => [
                    'max_rate' => [
                        'asc' => ['max_rate' => SORT_DESC],
                        'desc' => ['max_rate' => SORT_ASC],
                        'label' => 'По ставке',
                    ],
                    'rating' => [
                        'asc' => ['deposit_rating.value' => SORT_ASC],
                        'desc' => ['deposit_rating.value' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'По рейтингу',
                    ],
                    'folk_rating' => [
                        'asc' => ['COALESCE(folk_rating.value, 0)' => SORT_ASC],
                        'desc' => ['COALESCE(folk_rating.value, 0)' => SORT_DESC],
                        'default' => SORT_DESC,
                    ]
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 25
            ]
        ]);
    }
}