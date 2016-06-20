<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 5/5/15
 * Time: 10:11 AM
 */

namespace backend\models;


use common\models\DepositCatalog;

class CatalogForm extends DepositCatalog
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Новый каталог',
        ];
    }

}