<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


class UserRole extends Enum
{
    const ADMIN = 'admin';
    const MODERATOR = 'moderator';
    const EDITOR = 'editor';

    public static function getList()
    {
        return [
            self::ADMIN => "Админ",
            self::MODERATOR => "Модератор",
            self::EDITOR => "Редактор",
        ];
    }
}