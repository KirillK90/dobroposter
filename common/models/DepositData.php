<?php

namespace common\models;

use common\components\helpers\HDev;
use Yii;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "deposit_data".
 *
 * @property integer $id
 * @property boolean $is_popular
 * @property boolean $is_early_termination_possible
 * @property boolean $is_branded
 * @property integer $rating
 */
class DepositData extends \yii\db\ActiveRecord
{
    public $category_ids = [];
    public $custom_urls;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deposit_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_popular', 'is_early_termination_possible', 'is_branded'], 'required'],
            [['is_popular', 'is_early_termination_possible', 'is_branded'], 'boolean'],
            [['category_ids'], 'each', 'rule' => ['integer']],
            [['custom_urls'], 'each', 'rule' => ['string']],
            [['rating'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames)) {
            return false;
        }
        return $this->getDb()->transaction(function(Connection $db){
            parent::save(false);
            CategoryDeposit::deleteAll(['deposit_id' => $this->id]);

            foreach ($this->category_ids as $categoryId) {
                $categoryDeposit = new CategoryDeposit();
                $categoryDeposit->category_id = $categoryId;
                $categoryDeposit->deposit_id = $this->id;
                $categoryDeposit->custom_url = ArrayHelper::getValue($this->custom_urls, $categoryId);
                if (!$categoryDeposit->save()) {
                    HDev::logSaveError($categoryDeposit, true);
                }
            }
            return true;
        });
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_popular' => 'Популярный',
            'is_early_termination_possible' => 'Возможно ли льготное расторжение',
            'is_branded' => 'Брендированный',
            'rating' => 'Рэйтинг',
        ];
    }

    public function loadCategories(Deposit $deposit)
    {
        foreach ($deposit->categoryDeposits as $categoryId => $categoryDeposit) {
            $this->custom_urls[$categoryId] = $categoryDeposit->custom_url;
        }
    }
}
