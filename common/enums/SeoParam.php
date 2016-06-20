<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


class SeoParam extends Enum
{
    const H1 = 'h1';
    const SUB_TITLE = 'sub_title';
    const H2 = 'h2';
    const TEXT = 'text';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS = 'meta_keywords';
    const BREADCRUMB = 'breadcrumb';

    public static function getList()
    {
        return [
            self::H1 => 'H1',
            self::SUB_TITLE => 'Подстрочник',
            self::H2 => 'H2',
            self::TEXT => 'СЕО текст',
            self::META_TITLE => 'Meta Title',
            self::META_DESCRIPTION => 'Meta Description',
            self::META_KEYWORDS => 'Meta keywords',
            self::BREADCRUMB => 'Хлeбные крошки (последний пункт)',
        ];
    }

    public static function applySubstitutions($value, array $substitutions, $clear = true)
    {
        $result = strtr($value, $substitutions);
        $result = preg_replace('/\{\[([^\]]+?)\](\s*(?:[^\{\}\s]+\s*)+)\}/', '$1$2', $result);
        if ($clear) {
            $result = preg_replace('/{[^\{\}]*?\}/', '', $result);
            $result = preg_replace('/{[^\{\}]*?\}/', '', $result);
        }

        return trim($result);
    }
}