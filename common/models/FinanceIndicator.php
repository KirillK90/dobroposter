<?php

namespace common\models;

use common\components\helpers\HDates;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "finance_indicator".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property string $date
 * @property string $checked_at
 */
class FinanceIndicator extends \yii\db\ActiveRecord
{
    public static $models;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'finance_indicator';
    }

    /**
     * @param $id
     * @return FinanceIndicator
     */
    public static function getById($id)
    {
        if (!self::$models) {
            self::refreshData();
        }
        return ArrayHelper::getValue(self::$models, $id);
    }

    public static function refreshData()
    {
        self::$models = self::find()->indexBy('id')->all();
    }

    public static function getName($finance_indicator_id)
    {
        if ($indicator = self::getById($finance_indicator_id)) {
            return $indicator->name;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['date', 'checked_at'], 'safe'],
            [['value'], 'number'],
            [['id', 'name'], 'string', 'max' => 255]
        ];
    }

    public function beforeValidate()
    {
        if (is_numeric($this->date)) {
            $this->date = HDates::long($this->date);
        }
        if (is_numeric($this->checked_at)) {
            $this->checked_at = HDates::long($this->checked_at);
        }
        return parent::beforeValidate();
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
            'date' => 'Date',
            'checked_at' => 'Checked At',
        ];
    }
}
