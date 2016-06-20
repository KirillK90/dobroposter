<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


class OAuthName extends Enum
{
    const VK = 'vkontakte';
    const FB = 'facebook';
    const TWITTER = 'twitter';
    const OD = 'odnoklassniki';
    const YANDEX = 'yandex';
    const GOOGLE = 'google';

    public static function getList()
    {
        return [
            self::VK => "Вконтакте",
            self::FB => "Facebook",
            self::TWITTER => "Twitter",
            self::OD => "Одноклассники",
            self::YANDEX => "Яндекс",
            self::GOOGLE => "Google",
        ];
    }
}