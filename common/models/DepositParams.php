<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "deposit_regions".
 *
 * @property integer $id
 * @property integer $deposit_id
 * @property integer $param_id
 * @property string $type
 */
class DepositParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deposit_params';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deposit_id', 'param_id', 'type'], 'required'],
            [['deposit_id', 'param_id'], 'integer'],
            [['type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'deposit_id' => 'Deposit ID',
            'param_id' => 'Region ID',
            'type' => 'Type',
        ];
    }

    public function getValue()
    {
        return Dictionary::getValue($this->type, $this->param_id);
    }
}
