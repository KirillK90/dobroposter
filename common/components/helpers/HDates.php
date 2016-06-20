<?php

namespace common\components\helpers;

class HDates {
	
	/**
	 * Возвращает дату в формате Y-m-d H:i:s
	 * @param mixed $timestamp - null, timestamp или string 
	 * @return string
	 */
	public static function long($timestamp=NULL, $format = null)
	{
		return date("Y-m-d H:i:s",HDates::prepareTimestamp($timestamp, $format));
	}

	/**
	 * Возвращает дату в формате Y-m-d
	 * @param mixed $timestamp - null, timestamp или string
	 * @return string
	 */
	public static function short($timestamp=NULL, $format = null)
	{
		return date("Y-m-d",HDates::prepareTimestamp($timestamp, $format));
	}

	/**
	 * Возвращает дату в формате Y-m-d H:i
	 * @param mixed $timestamp - null, timestamp или string
	 * @return string
	 */
	public static function ui($timestamp=NULL)
	{
		return date("Y-m-d H:i",HDates::prepareTimestamp($timestamp));
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
	public static function prepareTimestamp($timestamp=NULL, $format = null)
	{
		if (is_null($timestamp))
			return time();

        if ($format) {
            $date = \DateTime::createFromFormat($format, $timestamp);
            return $date->getTimestamp();
        }
		
		if (!HDates::isTimestamp($timestamp))
			return strtotime($timestamp);
		
		return $timestamp;
	}

    public static function period($period, $genetive = false, $short = false)
    {
        $period = preg_replace('/(d|m|y)(\d)/', '$1 $2', $period);
        $period = preg_replace_callback('/(\d+)(d|m|y)/', function($matches) use ($genetive, $short) {
            switch ($matches[2]) {
                case 'd':
                    return $matches[1].' '.HStrings::pluralForm($matches[1], $genetive ? 'дня' : 'день', $genetive ? 'дней' : 'дня', 'дней');
                case 'm':
					$label = $short ? 'мес.' : HStrings::pluralForm($matches[1], $genetive ? 'месяца' : 'месяц', $genetive ? 'месяцев' : 'месяца', 'месяцев');
                    return $matches[1].' '.$label;
                case 'y':
                    return $matches[1].' '.HStrings::pluralForm($matches[1], $genetive ? 'года' : 'год', $genetive ? 'лет' : 'года', 'лет');
                default:
                    return $matches[0];
            }
        }, $period);
        return $period;
    }

	public static function daysList()
	{
        $days = [];
        foreach(range(1, 31) as $day) {
            $days[str_pad($day, 2, '0', STR_PAD_LEFT)] = $day;
        }
		return $days;
	}

    public static function monthsList()
    {
        return [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь',
        ];
    }

    /**
	 * Конвертирует дату в анл яз
	 *
	 */
	function date_rus2eng($text)
	{
		//краткаие месяцы
		$eng = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$rus = array("Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек");
		
		return str_replace(","," ",str_replace($rus, $eng, $text));
	}
	

}
