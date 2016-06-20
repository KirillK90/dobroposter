<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "folk_rating".
 *
 * @property integer $id
 * @property integer $bank_id
 * @property double $value
 * @property string $date
 */
class FolkRating extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'folk_rating';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bank_id', 'value', 'date'], 'required'],
            [['id', 'bank_id'], 'integer'],
            [['value'], 'number'],
            [['bank_id'], 'unique']
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
            'value' => 'Value',
        ];
    }
}
