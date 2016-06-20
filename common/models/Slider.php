<?php

namespace common\models;

use common\components\helpers\HDates;
use common\components\helpers\HDev;
use common\enums\Currency;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\Html;

/**
 * This is the model class for table "slider".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $title
 * @property boolean $show_logo
 * @property boolean $show_name
 * @property boolean $show_rate
 * @property boolean $show_period
 * @property boolean $enabled
 * @property boolean $random_slides
 * @property string $updated_at
 *
 * @property CatalogCategory $category
 * @property integer $slidesCount
 * @property Slide[] $slides
 * @property Deposit[] $deposits
 * @property CategoryDeposit[] $categoryDeposits
 */
class Slider extends \yii\db\ActiveRecord
{

    const MIN_SLIDES = 3;
    const MAX_SLIDES = 8;

    public $deposit_ids;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'slider';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'title'], 'required'],
            [['enabled', 'random_slides'], 'boolean'],
            [['deposit_ids'], 'required', 'when' => function(Slider $model){
                return !$model->random_slides;
            }],

            [['category_id'], 'integer'],
            [['show_logo', 'show_name', 'show_rate', 'show_period'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['deposit_ids'], 'each', 'rule' => ['integer']],
            [['deposit_ids'], 'validateSlides']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Категория',
            'deposit_ids' => 'Вклады',
            'random_slides' => 'Случайные вклады',
            'title' => 'Заголовок',
            'show_logo' => 'Отображать логотип',
            'show_name' => 'Отображать заголовок',
            'show_rate' => 'Отображать рейтинг',
            'show_period' => 'Отображать период',
            'count' => 'Количество слайдов',
            'enabled' => 'Включен'
        ];
    }

    public function validateSlides()
    {
        if (count($this->deposit_ids) < self::MIN_SLIDES) {
            $this->addError('depositIds', 'Выберите как минимум 3 вклада для показа в слайдере');
        }
        if (count($this->deposit_ids) > self::MAX_SLIDES) {
            $this->addError('depositIds', 'Выберите не более 8 вкладов для показа в слайдере');
        }
        if (!$this->show_logo && !$this->show_name && !$this->show_period && !$this->show_rate) {
            $this->addError('slides_show_logo', 'Хотя бы один параметр должен быть включен');
        }

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CatalogCategory::className(), ['id' => 'category_id']);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = self::find();
        $query->with('category', 'slides');
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);
    }

    public function getDepositList()
    {
        $data = [];
        foreach($this->deposits as $deposit) {
            $data[] = [
                'id' => $deposit->id,
                'mark' => 0,
                'value' => Html::a($deposit->product_name, $deposit->getAdminUrl())." ({$deposit->bank->name})",

            ];
        }
        return $data;
    }

    public function getSelectedDeposits()
    {
        return Deposit::find()->select(['id', 'bank_id', 'product_name'])->with(['bank' => function(Query $q){
                $q->select(['id', 'name']);
        }])->where(['id' => $this->deposit_ids])->all();
    }

    /**
     * @return ActiveQuery
     */
    public function getDeposits()
    {
        return $this->hasMany(Deposit::className(),  ['id' => 'deposit_id'])->indexBy('id')
            ->viaTable('slide', ['slider_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryDeposits()
    {
        return $this->hasMany(CategoryDeposit::className(),  ['category_id' => 'category_id'])->indexBy('deposit_id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlides()
    {
        return $this->hasMany(Slide::className(),  ['slider_id' => 'id'])->indexBy('deposit_id');
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames)) return false;
        $this->updated_at = HDates::long();
        $this->getDb()->transaction(function(Connection $db){
            if ($this->enabled) {
                Slider::updateAll(['enabled' => false]);
            }
            if (!parent::save()) {
                HDev::logSaveError($this, true);
            }
            Slide::deleteAll(['slider_id' => $this->id]);
            if ($this->deposit_ids) {
                $rows = [];
                foreach($this->deposit_ids as $depositId) {
                    $rows[] = [$this->id, $depositId];
                }
                $db->createCommand()->batchInsert(Slide::tableName(), ['slider_id', 'deposit_id'], $rows)->execute();
            }

        });

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlidesCount()
    {
        return count($this->slides);
    }

    public function getLink()
    {
        return Html::a($this->title, ['/sliders/update', 'id' => $this->id]);
    }

    public function getCurrencies()
    {
        $currencies = [];
        foreach($this->deposits as $deposit) {
            $currencies = array_merge($currencies, $deposit->getCurrencies());
        }

        return Currency::filterAndSort($currencies);
    }

    public function getCurrencyGroupedDeposits()
    {
        $data = [];
        foreach($this->deposits as $deposit) {
            foreach($deposit->getCurrencies() as $currency) {
                $data[$currency][] = $deposit;
            }
        }
        return $data;
    }
}
