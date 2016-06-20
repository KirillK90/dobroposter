<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


class Gender extends Enum
{
    const MALE = 'male';
    const FEMALE = 'female';

    public static function getList()
    {
        return [
            self::MALE => "Мужской",
            self::FEMALE => "Женский",
        ];
    }
}