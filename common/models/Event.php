<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "event".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property integer $place_id
 * @property string $announcement
 * @property string $description
 * @property string $start_time
 * @property string $end_time
 * @property string $status
 * @property string $image_src
 * @property string $url
 * @property boolean $free
 * @property integer $price_min
 * @property boolean $in_top
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $updated_at
 * @property string $published_at
 *
 * @property Category[] $categories
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'name', 'slug', 'format_id', 'announcement', 'description', 'start_time', 'end_time', 'status', 'url'], 'required'],
            [['created_at', 'start_time', 'end_time', 'updated_at', 'published_at'], 'safe'],
            [['place_id', 'price_min', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string'],
            [['free', 'in_top'], 'boolean'],
            [['name', 'slug', 'type', 'announcement', 'status', 'image_src', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Добавлено',
            'name' => 'Название',
            'slug' => 'Алиас',
            'type' => 'Тип',
            'format_id' => 'Формат',
            'place_id' => 'Место',
            'announcement' => 'Анонс',
            'description' => 'Описание',
            'start_time' => 'Время начала',
            'end_time' => 'Время окончания',
            'status' => 'Статус',
            'image_src' => 'Картинка',
            'url' => 'Ссылка',
            'free' => 'Бесплатное',
            'price_min' => 'Минимальная цена',
            'in_top' => 'Показывать в Топе',
            'created_by' => 'Автор',
            'updated_by' => 'Редактор',
            'updated_at' => 'Обновлено',
            'published_at' => 'Опубликовано',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable('event_category', ['event_id' => 'id']);
    }

    public function getUpdateUrl()
    {
        return Url::to(['events/update', 'id' => $this->id]);
    }
}
