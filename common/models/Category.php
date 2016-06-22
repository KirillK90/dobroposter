<?php

namespace common\models;

use common\components\helpers\HDates;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $menu_pos
 * @property boolean $primary
 * @property string $created_at
 *
 * @property Event[] $events
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    public static function getList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string', 'max' => 255],
            ['primary', 'boolean'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = HDates::long();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'primary' => 'В главном меню',
            'created_at' => 'Добавлена',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['id' => 'event_id'])->viaTable('event_category', ['category_id' => 'id']);
    }

    public function getUpdateUrl()
    {
        return Url::to(['categories/update', 'id' => $this->id]);
    }
}
