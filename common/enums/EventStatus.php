<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


class EventStatus extends Enum
{
    const DRAFT = 'draft';
    const PUBLISHED = 'published';
    const UNPUBLISHED = 'unpublished';

    public static function getList()
    {
        return [
            self::DRAFT => "Черновик",
            self::PUBLISHED => "Опубликован",
            self::UNPUBLISHED => "Снят с публикации",
        ];
    }
}