<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


class EntityType extends Enum
{
    const REGION = 'region';
    const BANK = 'bank';
    const DEPOSIT = 'deposit';

    public static function getList()
    {
        return [
            self::REGION => 'Регион',
            self::BANK => 'Банк',
            self::DEPOSIT => 'Вклад',
        ];
    }


}