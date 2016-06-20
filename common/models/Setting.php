<?php

namespace common\models;

use common\components\helpers\HDev;
use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    public static function setValue($name, $value)
    {
        if (!$model = self::findOne(['name' => $name])) {
            $model = new Setting(['name' => $name]);
        }
        $model->value = json_encode($value);
        if (!$model->save()) {
            HDev::logSaveError($model, true);
        }
        return true;
    }

    public static function getValue($name)
    {
        if (!$model = self::findOne(['name' => $name])) {
            $model = new Setting(['name' => $name]);
        }

        return json_decode($model->value, true);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
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
            'value' => 'Value',
        ];
    }
}
