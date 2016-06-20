<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/24/15
 * Time: 2:04 PM
 */

namespace common\enums;


use yii\helpers\ArrayHelper;

class PageType extends Enum
{
    const MAIN = 'main';
    const KINDS = 'kinds';
    const BANK_KINDS = 'bank_kinds';
    const BANKS = 'banks';
    const REGIONS = 'regions';
    const DEPOSIT_SEARCH = 'deposit_search';
    const BANK = 'bank';
    const CATEGORY = 'category';
    const BANK_CATEGORY = 'bank_category';
    const DEPOSIT = 'deposit';
    const ABOUT = 'about';
    const CONTACTS = 'contacts';
    const ADV = 'adv';
    const TERMS = 'terms';
    const PROFILE = 'profile';
    const ARTICLE = 'article';
    const NEWS = 'news';
    const GUIDES = 'guides';
    const ANALYTICS = 'analytics';
    const SIGNUP = 'signup';

    //Parametrization
    const BANK_ID = 'bank_id';
    const REGION_ID = 'region_id';
    const CATEGORY_ID = 'category_id';
    const SEARCH = 'search';

    public static function getStaticPages()
    {
        return [
            self::ABOUT => 'О проекте',
            self::CONTACTS => 'Контакты',
            self::ADV => 'Реклама',
            self::TERMS => 'Правила сайта',
            self::PROFILE => 'Личный кабинет',
            self::SIGNUP => 'Регистрация',
            self::SEARCH => 'Результаты поиска',
        ];
    }

    public static function isStaticPage($type)
    {
        $pages = self::getStaticPages();
        return isset($pages[$type]);
    }

    public static function getBannerPageType($type)
    {
        if (in_array($type, [self::BANK_KINDS, self::BANK_CATEGORY])) {
            return self::BANK;
        }
        return $type;
    }

    public static function getList()
    {
        return [
            self::MAIN => 'Главная',
            self::KINDS => 'Виды вкладов',
            self::BANK_KINDS => 'Виды вкладов банка',
            self::BANKS => 'Список банков',
            self::REGIONS => 'Список городов',
            self::DEPOSIT_SEARCH => 'Поиск вклада',
            self::BANK => 'Страница банка',
            self::BANK_CATEGORY => 'Категория вкладов банка',
            self::CATEGORY => 'Страница подборки',
            self::DEPOSIT => 'Страница вклада',
            self::ABOUT => 'О проекте',
            self::CONTACTS => 'Контакты',
            self::ADV => 'Реклама',
            self::TERMS => 'Правила сайта',
            self::PROFILE => 'Личный кабинет',
            self::SIGNUP => 'Регистрация',
            self::SEARCH => 'Результаты поиска',
            self::ARTICLE => 'Статья',
            self::NEWS => 'Новости',
            self::GUIDES => 'Гид по вкладам',
            self::ANALYTICS => 'Аналитика',
        ];
    }

    public static function getSubstitutions($page)
    {
        switch($page) {
            case self::MAIN:
            case self::KINDS:
            case self::BANKS:
            case self::DEPOSIT_SEARCH:
                return ArrayHelper::merge(
                    self::getCommonSubstitutions(),
                    self::getCitySubstitutions(),
                    self::getCitySearchSubstitutions()
                );
            case self::BANK_KINDS:
                return ArrayHelper::merge(
                    self::getCommonSubstitutions(),
                    self::getCitySubstitutions(),
                    self::getBankSubstitutions()
                );
            case self::BANK:
                return ArrayHelper::merge(
                    self::getCommonSubstitutions(),
                    self::getCitySubstitutions(),
                    self::getBankSubstitutions()
                );
            case self::BANK_CATEGORY:
                return ArrayHelper::merge(
                    self::getCommonSubstitutions(),
                    self::getCitySubstitutions(),
                    self::getCategoryBankSubstitutions(),
                    self::getBankSubstitutions()
                );
            case self::CATEGORY:
                return ArrayHelper::merge(
                    self::getCommonSubstitutions(),
                    self::getCitySubstitutions(),
                    self::getCategorySubstitutions()
                );
            case self::DEPOSIT:
                return ArrayHelper::merge(
                    self::getCommonSubstitutions(),
                    self::getCitySubstitutions(),
                    self::getDepositSubstitutions(),
                    self::getBankSubstitutions()
                );
            default:
                return self::getCommonSubstitutions();
        }
    }

    public static function getBannersList()
    {
        $list = self::getList();
        unset($list[self::BANK_CATEGORY]);
        unset($list[self::BANK_KINDS]);
        return $list;
    }

    public static function getParamType($page)
    {
        switch($page) {
            case self::DEPOSIT_SEARCH:
                return self::REGION_ID;
            case self::BANK:
                return self::BANK_ID;
            case self::CATEGORY:
                return self::CATEGORY_ID;
            default:
                return false;
        }
    }

    private static function getCitySubstitutions()
    {
        return [
            'region:name' => 'Город',
            'region:name_genitive' => 'Город в родительном пажеде',
            'region:name_prepositional' => 'Город в предложном пажеде',
            '-',
        ];
    }

    private static function getCitySearchSubstitutions()
    {
        return [
            'region:deposits_count' => 'Количество вкладов в городе',
            'region:banks_count' => 'Количество банков в городе',
            '-',
            'region:min_rate_rub' => 'min ставка в % в городе в рублях',
            'region:max_rate_rub' => 'max ставка в % в городе в рублях',
            'region:min_rate_foreign' => 'min ставка в % в городе в валюте',
            'region:max_rate_foreign' => 'max ставка в % в городе в валюте',
            '-',
            'region:top_deposit_rub_name' => 'самый выгодный (с высшим рейтингом) вклад в городе в рублях',
            'region:top_deposit_rub_min_rate' => 'min ставка в % самого выгодного вклада в рублях',
            'region:top_deposit_rub_max_rate' => 'max ставка в % самого выгодного вклада в рублях',
            'region:top_deposit_rub_min_amount' => 'min сумма для внесения по самому выгодному вкладу в рублях',
            'region:top_deposit_rub_min_period' => 'срок размещения по самому выгодному вкладу в рублях',
            'region:top_deposit_rub_rating' => 'рейтинг самого выгодного вклада в рублях',
            '-',
            'region:top_deposit_foreign_name' => 'самый выгодный (с высшим рейтингом) вклад в городе в валюте',
            'region:top_deposit_foreign_min_rate' => 'min ставка в % самого выгодного вклада в валюте',
            'region:top_deposit_foreign_max_rate' => 'max ставка в % самого выгодного вклада в валюте',
            'region:top_deposit_foreign_min_amount' => 'min сумма для внесения по самому выгодному вкладу в валюте',
            'region:top_deposit_foreign_min_period' => 'срок размещения по самому выгодному вкладу в валюте',
            'region:top_deposit_foreign_rating' => 'рейтинг самого выгодного вклада в валюте',
            '-',
        ];
    }

    private static function getBankSubstitutions()
    {
        return [
            'bank:name' => 'банк',
            'bank:name_genitive' => 'банк в родительном пажеде',
            'bank:name_prepositional' => 'банк в предложном пажеде',
            '-',
            'bank:deposits_count' => 'количество вкладов у банка',
            'bank:min_rate_rub' => 'min ставка в % по банку в рублях',
            'bank:max_rate_rub' => 'max ставкав в % по банку в рублях',
            'bank:min_rate_foreign' => 'min ставка в % по банку в валюте',
            'bank:max_rate_foreign' => 'max ставка в % по банку в валюте',
            '-',
            'bank:top_deposit_rub_name' => 'самый выгодный (с высшим рейтингом) вклад в банке в рублях',
            'bank:top_deposit_rub_min_rate' => 'min ставка в % самого выгодного вклада в рублях',
            'bank:top_deposit_rub_max_rate' => 'max ставка в % самого выгодного вклада в рублях',
            'bank:top_deposit_rub_min_amount' => 'min сумма для внесения по самому выгодному вкладу в рублях',
            'bank:top_deposit_rub_min_period' => 'срок размещения по самому выгодному вкладу в рублях',
            'bank:top_deposit_rub_rating' => 'рейтинг самого выгодного вклада в рублях',
            '-',
            'bank:top_deposit_foreign_name' => 'самый выгодный (с высшим рейтингом) вклад в банке в валюте',
            'bank:top_deposit_foreign_min_rate' => 'min ставка в % самого выгодного вклада в валюте',
            'bank:top_deposit_foreign_max_rate' => 'max ставка в % самого выгодного вклада в валюте',
            'bank:top_deposit_foreign_min_amount' => 'min сумма для внесения по самому выгодному вкладу в валюте',
            'bank:top_deposit_foreign_min_period' => 'срок размещения по самому выгодному вкладу в валюте',
            'bank:top_deposit_foreign_rating' => 'рейтинг самого выгодного вклада в валюте',
            '-',
        ];
    }

    private static function getCategoryBankSubstitutions()
    {
        return [
            'category:name' => 'Подборка',
            'category:catalog_name' => 'Каталог',
            '-',
        ];
    }

    private static function getCategorySubstitutions()
    {
        return [
            'category:name' => 'Подборка',
            'category:catalog_name' => 'Каталог',
            '-',
            'category:min_rate_rub' => 'min ставка в % в городе в рублях',
            'category:max_rate_rub' => 'max ставка в % в городе в рублях',
            'category:min_rate_foreign' => 'min ставка в % в городе в валюте',
            'category:max_rate_foreign' => 'max ставка в % в городе в валюте',
            '-',
            'category:top_deposit_rub_name' => 'самый выгодный (с высшим рейтингом) вклад в городе в рублях',
            'category:top_deposit_rub_min_rate' => 'min ставка в % самого выгодного вклада в рублях',
            'category:top_deposit_rub_max_rate' => 'max ставка в % самого выгодного вклада в рублях',
            'category:top_deposit_rub_min_amount' => 'min сумма для внесения по самому выгодному вкладу в рублях',
            'category:top_deposit_rub_min_period' => 'срок размещения по самому выгодному вкладу в рублях',
            'category:top_deposit_rub_rating' => 'рейтинг самого выгодного вклада в рублях',
            '-',
            'category:top_deposit_foreign_name' => 'самый выгодный (с высшим рейтингом) вклад в городе в валюте',
            'category:top_deposit_foreign_min_rate' => 'min ставка в % самого выгодного вклада в валюте',
            'category:top_deposit_foreign_max_rate' => 'max ставка в % самого выгодного вклада в валюте',
            'category:top_deposit_foreign_min_amount' => 'min сумма для внесения по самому выгодному вкладу в валюте',
            'category:top_deposit_foreign_min_period' => 'срок размещения по самому выгодному вкладу в валюте',
            'category:top_deposit_foreign_rating' => 'рейтинг самого выгодного вклада в валюте',
            '-',
        ];
    }

    private static function getCommonSubstitutions()
    {
        return [
            'year' => 'Год текущий',
            'update_date' => 'Последнее обновление вкладов (дата)',
            '-',
        ];
    }

    private static function getDepositSubstitutions()
    {
        return [
            'deposit:name' => 'Депозит',
            '-',
        ];
    }

    public static function isGeoDependent($page)
    {
        return in_array($page, [self::DEPOSIT_SEARCH, self::BANK, self::CATEGORY]);
    }
}