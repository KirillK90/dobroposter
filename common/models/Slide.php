<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "slide".
 *
 * @property integer $slider_id
 * @property integer $deposit_id
 *
 * @property Slider $slider
 */
class Slide extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'slide';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slider_id', 'deposit_id'], 'required'],
            [['slider_id', 'deposit_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'slider_id' => 'Slider ID',
            'deposit_id' => 'Deposit ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlider()
    {
        return $this->hasOne(Slider::className(), ['id' => 'slider_id']);
    }
}
