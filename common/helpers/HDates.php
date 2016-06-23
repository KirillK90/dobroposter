<?php

namespace common\helpers;

/**
 * Класс для доп.методов работы с датами
 *
 * @package common\helpers
 */
class HDates
{
    /**
     * Возвращает дату в формате Y-m-d H:i:s
     * @param mixed $timestamp - null, timestamp или string
     * @return string
     */
    public static function long($timestamp = null)
    {
        return date("Y-m-d H:i:s", HDates::prepareTimestamp($timestamp));
    }

    /**
     * Возвращает дату в формате Y-m-d
     * @param mixed $timestamp - null, timestamp или string
     * @return string
     */
    public static function short($timestamp = null)
    {
        return date("Y-m-d", HDates::prepareTimestamp($timestamp));
    }

    /**
     * Возвращает дату в формате d.m.Y
     * @param null $timestamp
     * @return bool|string
     */
    public static function dmY($timestamp = null)
    {
        return date("d.m.Y", HDates::prepareTimestamp($timestamp));
    }

    /**
     * Возвращает дату в формате Y-m-d H:i
     * @param mixed $timestamp - null, timestamp или string
     * @return string
     */
    public static function ui($timestamp = null)
    {
        return date("Y-m-d H:i", HDates::prepareTimestamp($timestamp));
    }

    /**
     * Проверяет формат UNIX Timestamp
     *
     * @param $timestamp
     * @return bool
     */
    public static function isTimestamp($timestamp)
    {
        return ((string) (int) $timestamp === (string) $timestamp);
        //	&& ($timestamp <= PHP_INT_MAX)
        //	&& ($timestamp >= ~PHP_INT_MAX)
        //	&& (!strtotime($timestamp));
    }

    /**
     * Возвращает дату в формате UNIX Timestamp
     * @param mixed $timestamp - null, timestamp или string
     * @param null $format
     * @return int
     */
    public static function prepareTimestamp($timestamp = null, $format = null)
    {
        if (is_null($timestamp)) {
            return time();
        }

        if ($format) {
            $date = \DateTime::createFromFormat($format, $timestamp);

            return $date->getTimestamp();
        }

        if (!HDates::isTimestamp($timestamp)) {
            return strtotime($timestamp);
        }

        return $timestamp;
    }
}

?>