<?php

namespace common\components\helpers;

use yii\validators\NumberValidator;

class HStrings
{

    const SPACE_CHAR = '-';

    public static $trans = [
        "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
        "е"=>"e", "ё"=>"yo","ж"=>"zh","з"=>"z","и"=>"i",
        "й"=>"j","к"=>"k","л"=>"l", "м"=>"m","н"=>"n",
        "о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t",
        "у"=>"u","ф"=>"f","х"=>"h","ц"=>"c","ч"=>"ch",
        "ш"=>"sh","щ"=>"sh","ы"=>"y","э"=>"e","ю"=>"yu",
        "я"=>"ya",
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
        "Е"=>"E","Ё"=>"Yo","Ж"=>"Zh","З"=>"Z","И"=>"I",
        "Й"=>"J","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"C","Ч"=>"Ch",
        "Ш"=>"Sh","Щ"=>"Shh","Ы"=>"Y","Э"=>"E","Ю"=>"Yu",
        "Я"=>"Ya",
        "a"=>"a","b"=>"b","c"=>"c","d"=>"d","e"=>"e","f"=>"f","g"=>"g","h"=>"h","i"=>"i","j"=>"j","k"=>"k","l"=>"l","m"=>"m","n"=>"n","o"=>"o","p"=>"p","q"=>"q","r"=>"r","s"=>"s","t"=>"t","u"=>"u","v"=>"v","w"=>"w","x"=>"x","y"=>"y","z"=>"z",
        "A"=>"A","B"=>"B","C"=>"C","D"=>"D","E"=>"E","F"=>"F","G"=>"G","H"=>"H","I"=>"I","J"=>"J","K"=>"K","L"=>"L","M"=>"M","N"=>"N","O"=>"O","P"=>"P","Q"=>"Q","R"=>"R","S"=>"S","T"=>"T","U"=>"U","V"=>"V","W"=>"W","X"=>"X","Y"=>"Y","Z"=>"Z",
        "1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5",
        "6"=>"6","7"=>"7","8"=>"8","9"=>"9","0"=>"0",
        "ь"=>"","Ь"=>"","ъ"=>"","Ъ"=>"",
        ' ' => self::SPACE_CHAR, '`' => self::SPACE_CHAR, '~' => self::SPACE_CHAR,
        '!' => self::SPACE_CHAR, '@' => self::SPACE_CHAR, '#' => self::SPACE_CHAR,
        '$' => self::SPACE_CHAR, '%' => self::SPACE_CHAR, '^' => self::SPACE_CHAR,
        '&' => self::SPACE_CHAR, '*' => self::SPACE_CHAR, '(' => self::SPACE_CHAR,
        ')' => self::SPACE_CHAR, '-' => self::SPACE_CHAR, '\='=> self::SPACE_CHAR,
        '+' => 'plus',
        '[' => self::SPACE_CHAR, ']' => self::SPACE_CHAR, '\\'=> self::SPACE_CHAR,
        '|' => self::SPACE_CHAR, '/' => self::SPACE_CHAR, '.' => self::SPACE_CHAR,
        ',' => self::SPACE_CHAR, '\''=> self::SPACE_CHAR, '"' => self::SPACE_CHAR,
        ';' => self::SPACE_CHAR, '?' => self::SPACE_CHAR, '<' => self::SPACE_CHAR,
        '>' => self::SPACE_CHAR, '№' => self::SPACE_CHAR
    ];



    public static function currency($value)
    {
        return $value ? number_format($value, 0, '.', ' ') : $value;
    }

    public static function pluralForm($n, $form1, $form2, $form5)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $form5;
        if ($n1 > 1 && $n1 < 5) return $form2;
        if ($n1 == 1) return $form1;
        return $form5;
    }

    public static function transliterate($text, $lover = true)
    {
        if ($lover) {
            $text = mb_strtolower($text);
        }

        $text = preg_replace_callback('/./u',function($match){
            if(!isset(HStrings::$trans[$match[0]])){
                return '';
            }
            return $match[0];
        },$text);

        $text = strtr($text, self::$trans);
        while(strpos($text, '--') !== false) $text = str_replace('--', self::SPACE_CHAR, $text);
        return trim($text, self::SPACE_CHAR);
    }


    public static function short($str, $limit, $wordBreak = true)
    {
        if (mb_strlen($str) > $limit) {
            $str = mb_substr($str, 0, $limit - 3);
            if (!$wordBreak) {
                $str = mb_substr($str, 0, mb_strrpos($str, ' '));
            }
            $str .= '...';
        }
        return $str;
    }

    public static function isPositiveNumber($period)
    {
        $validator = new NumberValidator();
        $validator->min = 0;
        $validator->integerOnly = true;
        return $validator->validate($period);
    }

    public static function getFirstParagraph($string, $maxLength = null)
    {
        $string = substr($string,0, strpos($string, "</p>")+4);
        $string = strip_tags($string);
        if ($maxLength && mb_strlen($string) > $maxLength) {
            $string = self::short($string, $maxLength, false);
        }
        return $string;
    }

    public static function cleanFileName($name)
    {
        return str_replace(array(' ', ';', ',', '"', "\r", "\t"), "_", trim($name));
    }

    public static function parseItems($text, $charSet=";,\r\t")
    {
        $text = str_replace(str_split($charSet), "\n", trim($text));
        while (substr_count($text, "\n\n")) $text = str_replace("\n\n", "\n", $text);

        $items = $text ? explode("\n", trim($text)) : array();
        return $items;
    }

    public static function addHttp($url)
    {
        if (strpos($url, '//') === 0) {
            return 'http:'.$url;
        } else {
            return $url;
        }
    }

    public static function rate($value, $max = false)
    {
        if ($max) $value = "до ".$value;
        return $value ? str_replace('.',',',$value).'%' : $value;
    }
}