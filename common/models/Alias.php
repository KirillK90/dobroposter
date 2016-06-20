<?php

namespace common\models;

use common\enums\EntityType;
use Yii;

/**
 * This is the model class for table "alias".
 *
 * @property integer $id
 * @property string $slug
 * @property string $source
 * @property boolean $genitive
 * @property integer $entity_id
 * @property string $entity_type
 * @property integer $parent_id
 * @property string $created_at
 */
class Alias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alias';
    }

    /**
     * @param $slug
     * @return Region
     */
    public static function findRegion($slug)
    {
        return Region::find()
            ->innerJoinWith('alias', false)
            ->where(['slug' => $slug])->one();
    }

    /**
     * @param $slug
     * @return Bank
     */
    public static function findBank($slug)
    {
        /** @var Alias $alias */
        $alias = self::findOne(['slug' => $slug, 'entity_type' => EntityType::BANK]);
        if ($alias) {
            if (!$bank = Bank::findOne(['id' => $alias->entity_id])) {
                $bank = new Bank(['id' => $alias->entity_id, 'name_genitive' => $alias->source, 'name' => $alias->source, 'deleted' => true]);
            }
            return $bank;
        } else {
            return null;
        }

    }

    public static function findDeposit($slug, $bankId)
    {
        /** @var Alias $alias */
        $alias = self::findOne(['slug' => $slug, 'entity_type' => EntityType::DEPOSIT, 'parent_id' => $bankId]);
        if ($alias) {
            if (!$deposit = Deposit::findOne(['id' => $alias->entity_id])) {
                $deposit = new Deposit(['id' => $alias->entity_id, 'bank_id' => $bankId, 'product_name' => $alias->source, 'deleted' => true]);
            }
            return $deposit;
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slug', 'source', 'entity_id', 'entity_type', 'created_at'], 'required'],
            [['genitive'], 'boolean'],
            [['entity_id', 'parent_id'], 'integer'],
            [['created_at'], 'safe'],
            [['slug', 'source', 'entity_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Slug',
            'source' => 'Source',
            'genitive' => 'Genitive',
            'entity_id' => 'Entity ID',
            'entity_type' => 'Entity Type',
            'parent_id' => 'Parent ID',
            'created_at' => 'Created At',
        ];
    }
}
