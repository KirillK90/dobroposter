<?php

namespace backend\models;

use common\enums\ArticleStatus;
use common\enums\UserRole;
use common\models\Article;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class ArticleFilter
 * @package backend\models
 */
class ArticleFilter extends Article
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'author_id', 'updater_id'], 'integer'],
            ['category', 'in', 'range' => UserRole::getValues()],
            ['status', 'in', 'range' => ArticleStatus::getValues()],
        ];
    }


    public function search()
    {
        $query = self::find();
        $query->with('author', 'updater');
        $query->andFilterWhere([
            'id' => $this->id,
            'author_id' => $this->author_id,
            'type' => $this->type,
            'status' => $this->status,
        ]);
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC],
            ]
        ]);
    }
}