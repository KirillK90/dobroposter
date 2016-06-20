<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "view_search".
 *
 * @property string $id
 * @property string $name
 * @property string $announce
 * @property string $entity
 */
class ViewSearch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_search';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'announce', 'entity'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'announce' => 'Announce',
            'entity' => 'Entity',
        ];
    }
}
