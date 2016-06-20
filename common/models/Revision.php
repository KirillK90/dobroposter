<?php

namespace common\models;

use Yii;
use yii\base\Event;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "revision".
 *
 * @property integer $id
 * @property integer $entity_id
 * @property string $entity_type
 * @property string $data
 * @property boolean $preview
 * @property integer $author_id
 * @property string $created_at
 */
class Revision extends \yii\db\ActiveRecord
{
    public static function getPreview($id, $type)
    {
        $attrs = ['entity_id' => $id, 'entity_type' => $type, 'preview' => true];
        if (!$revision = Revision::findOne($attrs)) {
            $revision = new Revision($attrs);
        }
        return $revision;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()::timestamp(0)'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'author_id',
                'updatedByAttribute' => false,
                'value' => function(Event $event) {
                    $user = Yii::$app->get('user', false);
                    return $user && !$user->isGuest ? $user->id : $this->author_id;
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'revision';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entity_id', 'entity_type', 'data', 'preview'], 'required'],
            [['entity_id'], 'integer'],
            [['data'], 'string'],
            [['preview'], 'boolean'],
            [['created_at'], 'safe'],
            [['entity_type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_id' => 'Entity ID',
            'entity_type' => 'Entity Type',
            'data' => 'Data',
            'preview' => 'Preview',
            'created_at' => 'Created At',
        ];
    }

    public function getData()
    {
        return json_decode($this->data, true);
    }
}
