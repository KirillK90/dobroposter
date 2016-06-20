<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


class SessionVar extends Enum
{
    const CURRENT_REGION = 'current_region';

    public static function getList()
    {
        return [
            self::CURRENT_REGION => "Текущий регион",
        ];
    }
}