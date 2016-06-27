<?php

namespace common\models;

use common\helpers\HDates;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "place".
 *
 * @property integer $id
 * @property string $name
 * @property string $created_at
 */
class Place extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'place';
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
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
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
            'created_at' => 'Место',
        ];
    }

    public function getUpdateUrl()
    {
        return Url::to(['places/update', 'id' => $this->id]);
    }
}
