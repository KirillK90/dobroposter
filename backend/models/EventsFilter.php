<?php

namespace backend\models;

use common\models\Event;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class BannersFilter
 * @package backend\models
 */
class EventsFilter extends Event
{

    public function search()
    {
        $query = self::find();

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);
    }
}