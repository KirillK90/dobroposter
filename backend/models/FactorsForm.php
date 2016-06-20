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

class FactorsForm extends Model
{
    public $raising_factor;
    public $reduction_factor;
    public $reduction_types;
    public $reduction_regexp;
    public $reduction_factor_2;
    public $reduction_conditions_regexp;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['raising_factor', 'reduction_factor', 'reduction_factor_2'], 'integer', 'min' => 0, 'max' => 100],
            [['reduction_types'], 'each', 'rule' => ['integer']],
            [['reduction_regexp', 'reduction_conditions_regexp'], 'safe'],
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
            'raising_factor' => 'Повышающий коэффициент',
            'reduction_factor' => 'Понижающий коэффициент',
            'reduction_types' => 'Специальные типы',
            'reduction_regexp' => 'Шаблон имени',
            'reduction_factor_2' => 'Особый понижающий коэффициент',
            'reduction_conditions_regexp' => 'Шаблон для условий вклада',
        ];
    }

    public function attributeHints()
    {
        return [
            'raising_factor' => 'Применяется ко вкладу с высшим рейтингом для банка',
            'reduction_types' => 'Список специальных типов вкладов к которым будет применяться понижающий коэффициент',
            'reduction_regexp' => 'Регулярное выражение для названия вклада, к которому будет применяться понижающий коэффициент. Может быть <em>&laquo;значение&nbsp;1|значение&nbsp;2|значение&nbsp;3&raquo;</em>',
            'reduction_conditions_regexp' => 'Регулярное выражение для условий вклада, к которому будет применяться особый понижающий коэффициент. Может быть <em>&laquo;значение&nbsp;1|значение&nbsp;2|значение&nbsp;3&raquo;</em>'
        ];
    }


    public function loadValues()
    {
        foreach($this->attributes() as $attribute) {
            $this->$attribute = Setting::getValue($attribute);
        }
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        foreach($this->getAttributes() as $attribute => $value) {
            Setting::setValue($attribute, $value);
        }
        return true;
    }

}