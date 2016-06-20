<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


use yii\helpers\ArrayHelper;

class PagePlace extends Enum
{
    const TOP = 'top';
    const HEADER = 'header';
    const RIGHT_TOP = 'right_top';
    const RIGHT_BOTTOM = 'right_bottom';
    const BOTTOM = 'bottom';

    public static $sizes = [
        self::TOP => [
            'message' => 'Ширина 100% по контейнеру, минимальная 750pх, высота 90px',
            'minWidth' => 750,
            'minHeight' => 90,
            'maxHeight' => 90,
        ],
        self::HEADER => [
            'message' => 'Точный размер 400х90px',
            'minWidth' => 400,
            'maxWidth' => 400,
            'minHeight' => 90,
            'maxHeight' => 90,
        ],
        self::RIGHT_TOP => [
            'message' => 'Максимальная ширина 300px',
            'maxWidth' => 300,
        ],
        self::RIGHT_BOTTOM => [
            'message' => 'Максимальная ширина 300px',
            'maxWidth' => 300,
        ],
        self::BOTTOM => [
            'message' => 'Ширина 100% по ширине центральной колонки, высота динамическая',
        ],
    ];

    public static function getList()
    {
        return [
            self::TOP => 'Верхний',
            self::HEADER => 'Шапка',
            self::RIGHT_TOP => 'Правый верхний',
            self::RIGHT_BOTTOM => 'Правый нижний',
            self::BOTTOM => 'Нижний',
        ];
    }

    public static function getSizeText($place)
    {
        return ArrayHelper::getValue(self::$sizes, "$place.message");
    }

    public static function getSize($place)
    {
        return ArrayHelper::getValue(self::$sizes, $place);
    }
}