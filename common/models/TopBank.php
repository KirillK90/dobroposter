<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "top_bank".
 *
 * @property integer $id
 * @property integer $bank_id
 */
class TopBank extends \yii\db\ActiveRecord
{
    public $bank_ids;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'top_bank';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bank_id'], 'required'],
            [['bank_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bank_id' => 'Bank ID',
        ];
    }
}
