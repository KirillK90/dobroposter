<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


use yii\helpers\Url;

class SeoCustomParam extends Enum
{
    const BANK_ID = 'bank_id';
    const REGION_ID = 'region_id';
    const CATEGORY_ID = 'category_id';

    public static function getList()
    {
        return [
            self::BANK_ID => 'по банку',
            self::REGION_ID => 'по городу',
            self::CATEGORY_ID => 'по категории'
        ];
    }

    public static function getParam($page)
    {
        switch($page) {
            case PageType::DEPOSIT_SEARCH:
                return self::REGION_ID;
            case PageType::BANK:
                return self::BANK_ID;
            case PageType::CATEGORY:
                return self::CATEGORY_ID;
            default:
                return false;
        }
    }

    public static function getAjaxUrl($customParam)
    {
        switch($customParam) {
            case self::REGION_ID:
                return Url::to(['/regions/filter']);
            case self::BANK_ID:
                return Url::to(['/banks/filter']);
            case self::CATEGORY_ID:
                return Url::to(['/structure/search']);
        }
        return null;
    }
}