<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "capitalization".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 */
class Dictionary extends \yii\db\ActiveRecord
{
    const NO_CAPITALIZATION_VALUE = 3;

    public static $params = null;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary';
    }

    public static function getValue($type, $param_id)
    {
        return $param_id !== null ? ArrayHelper::getValue(self::params(), "$type.$param_id", 'n/a') : 'n/a';
    }

    public static function getValues($type)
    {
        return ArrayHelper::getValue(self::params(), "$type", []);
    }

    public static function refreshParams($cache=true)
    {
        if ($cache) {
            $params = self::getDb()->cache(function(){
                return self::find()->asArray()->all();
            });
        } else {
            $params = self::find()->asArray()->all();
        }

        self::$params = ArrayHelper::map($params, 'id', 'name', 'type');
        return self::$params;
    }

    public static function params()
    {
        if (is_null(self::$params)) {
            self::refreshParams();
        }
        return self::$params;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'type'], 'required'],
            [['name', 'type'], 'string', 'max' => 255]
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
        ];
    }
}
