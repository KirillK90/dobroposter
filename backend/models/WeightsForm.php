<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 5/5/15
 * Time: 10:11 AM
 */

namespace backend\models;


use common\enums\RatingPart;
use common\models\Setting;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class WeightsForm extends Model
{
    public $weights;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weights'], 'required'],
            [['weights'], 'each', 'rule' => ['number', 'min' => 0, 'max' => 1]],
            [['weights'], 'validateWeights'],
        ];
    }

    public function validateWeights()
    {
        if ((string) array_sum($this->weights) != '1') {
            $this->addError('weights', "Сумма весов должна быть равно 1, а не ".array_sum($this->weights));
        }
        foreach(RatingPart::getList() as $value => $name) {
            if (!ArrayHelper::getValue($this->weights, $value)) {
                $this->addError('weights', "Не указано значение для $name");
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'weights' => 'Веса',
        ];
    }

    public function loadWeights()
    {
        $this->weights = Setting::getValue('weights');
    }

    public function save()
    {
        return $this->validate() && Setting::setValue('weights', $this->weights);
    }

}