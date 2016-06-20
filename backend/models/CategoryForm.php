<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 5/5/15
 * Time: 10:11 AM
 */

namespace backend\models;


use common\enums\CatalogType;
use common\models\CatalogCategory;
use common\models\CategoryDeposit;
use common\models\FormData;
use common\models\Slide;
use common\models\Slider;
use yii\db\Connection;

class CategoryForm extends CatalogCategory
{
    public $deposit_ids = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'catalog_id'], 'required'],
            [['enabled', 'dynamic'], 'boolean'],
            [['deposit_ids'], 'each', 'rule' => ['integer']],
            [['deposit_ids'], 'required', 'message' => 'Выберите вклады для подборки или включите чекбокс Динамическая', 'when' => function(CategoryForm $model) {
                return !$model->dynamic;
            }],
            [['name'], 'unique'],
            [['type'], 'default', 'value' => CatalogType::CUSTOM],
            [['form_hash'], 'required', 'message' => 'Задайте условия фильтра',
                'when' => function(CategoryForm $model) {
                return $model->dynamic;
            }],
            [['form_hash'], 'exist', 'targetClass' => FormData::className(), 'targetAttribute' => 'hash'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название подборки',
            'catalog_id' => 'Каталог',
            'deposit_ids' => 'Вклады',
            'enabled' => 'Показывать',
            'dynamic' => 'Динамическая',
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames)) {
            return false;
        }
        return $this->getDb()->transaction(function(Connection $db){
            parent::save(false);
            if (!$this->isDynamic()) {
                $this->removeOutdatedSlides();
                $this->saveDeposits();
            }
            return true;
        });
    }

    public function isCustom()
    {
        return $this->catalog->type==CatalogType::CUSTOM;
    }

    private function removeOutdatedSlides()
    {
        $sliderIds = Slider::find()->where(['category_id' => $this->id])->select(['id'])->asArray()->column();
        Slide::deleteAll(['and',
            ['slider_id' => $sliderIds],
            ['not', ['deposit_id' => $this->deposit_ids ?: []]]
        ]);
    }

    public function isDynamic()
    {
        return $this->dynamic || $this->catalog->type != CatalogType::CUSTOM;
    }

    private function saveDeposits()
    {
        $currentIds = array_keys($this->categoryDeposits);
        $newIds = array_diff($this->deposit_ids, $currentIds);
        $delIds = array_diff($currentIds, $this->deposit_ids);
        CategoryDeposit::deleteAll(['and',
            ['category_id' => $this->id],
            ['deposit_id' => $delIds]
        ]);
        if ($newIds) {
            $rows = [];
            foreach($newIds as $id) {
                $rows[] = [$this->id, $id];
            }
            $this->getDb()->createCommand()->batchInsert(CategoryDeposit::tableName(), ['category_id', 'deposit_id'], $rows)->execute();
        }
    }
}