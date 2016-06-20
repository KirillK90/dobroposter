<?php

namespace backend\models;

use common\enums\Gender;
use common\enums\OAuthName;
use common\enums\UserRole;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * Class BannersFilter
 * @package backend\models
 */
class UserFilter extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['role', 'in', 'range' => UserRole::getValues()],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['birthday'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Заполните год'],
            [['username', 'email'], 'safe'],
            [['gender'], 'in', 'range' => Gender::getValues()],
            [['oauth', 'is_subscribed'], 'boolean'],
            [['oauth_service'], 'in', 'range'=> OAuthName::getValues()],
            [['oauth_id'], 'integer'],
        ];
    }


    public function search()
    {
        $query = self::find();

        $query->andFilterWhere([
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'status' => $this->status,
            'role' => $this->role,
            'gender' => $this->gender,
            'oauth' => $this->oauth,
            'oauth_service' => $this->oauth_service,
            'oauth_id' => $this->oauth_id,
            'is_subscribed' => $this->is_subscribed,
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['last_visit' => SORT_DESC],
                'attributes' => [
                    'last_visit' => [
                        'asc' => ['last_visit' => SORT_ASC],
                        'desc' => [new Expression('last_visit DESC NULLS LAST')],
                    ],
                    'created_at',
                    'id'
                ]
            ]
        ]);
    }
}