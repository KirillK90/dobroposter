<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bank_rating".
 *
 * @property integer $id
 * @property integer $bank_id
 */
class BankRating extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bank_rating';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bank_id'], 'required'],
            [['id', 'bank_id'], 'integer']
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
