<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


abstract class Enum
{
    static function getName($key, $default = "N/A"){
        $list = static::getList();
        if (!array_key_exists($key, $list))
            return $default;

        return $list[$key];
    }

    public static function getValues()
    {
        return array_keys(static::getList());
    }

    public static function getList() {
        $class = get_called_class();
        $oClass = new \ReflectionClass($class);
        $constants = $oClass->getConstants();
        return array_combine(array_values($constants), array_keys($constants));
    }

    public static function hasValue($value) {
        return in_array($value, static::getValues());
    }

}