<?php

namespace frontend\models;

use common\models\Event;
use yii\data\ActiveDataProvider;

/**
 * Class BannersFilter
 * @package backend\models
 */
class EventsFilter extends Event
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'start_time', 'end_time', 'updated_at', 'published_at'], 'safe'],
            [['id', 'place_id', 'format_id', 'price_min', 'created_by', 'updated_by'], 'integer'],
            [['free', 'in_top'], 'boolean'],
            [['title', 'slug', 'status'], 'string', 'max' => 255],
        ];
    }

    public function search()
    {
        $query = Event::find();
        $query->distinct();
        $query->andFilterWhere([
            'id' => $this->id,
            'title' => $this->title,
            'format_id' => $this->format_id,
            'place_id' => $this->place_id,
        ]);
        if ($this->category_ids) {
            $query->leftJoin('event_category', 'event.id = event_category.event_id');
            $query->andWhere(['in', 'event_category.category_id', $this->category_ids]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
            'pagination' => [
                'defaultPageSize' => 6
            ]
        ]);
    }
}