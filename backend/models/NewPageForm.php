<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 5/5/15
 * Time: 10:11 AM
 */

namespace backend\models;


use common\enums\ArticleType;
use common\models\Article;

class NewPageForm extends Article
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'in', 'range' => ArticleType::getValues()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Выберите тип материала',
        ];
    }

}