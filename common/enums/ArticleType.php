<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


class ArticleType extends Enum
{
    const PAGE = 'page';
    const GUIDES = 'guides';
    const NEWS = 'news';
    const ANALYTICS = 'analytics';

    public static function getList()
    {
        return [
            self::PAGE => "Страницы",
            self::GUIDES => "Гид по вкладам",
            self::NEWS => "Новости",
            self::ANALYTICS => "Аналитика",
        ];
    }
}