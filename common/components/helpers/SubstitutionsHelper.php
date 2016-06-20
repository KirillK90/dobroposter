<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/20/15
 * Time: 5:36 PM
 */

namespace common\components\helpers;


use common\enums\Currency;
use common\models\Bank;
use common\models\CatalogCategory;
use common\models\Deposit;
use common\models\DepositFilter;
use common\models\Region;
use yii\db\ActiveQuery;
use yii\db\Connection;

class SubstitutionsHelper
{
    public static function getCommonSubstitutions()
    {
        return [
            '{year}' => date('Y'),
            '{update_date}' => self::date(self::query('select max(created) from api_log')),
        ];
    }

    public static function getRussiaSubstitutions($full = false)
    {
        $substitutions = [];
        if ($full) {
            /** @var Deposit $topRubDeposit */
            $topRubDeposit = self::queryModel(Deposit::find()->with('rates')->innerJoinWith('ratings')->where(["deposit_rating.currency_id" => Currency::RUB])->orderBy('deposit_rating.value DESC')->limit(1));
            if ($topRubDeposit) {
                $topRubDepositCurrency = $topRubDeposit->getDepositCurrency(Currency::RUB);
                $substitutions += [
                    '{region:top_deposit_rub_name}' => $topRubDeposit->product_name,
                    '{region:top_deposit_rub_min_rate}' => $topRubDepositCurrency->getMinRate(),
                    '{region:top_deposit_rub_max_rate}' => $topRubDepositCurrency->getMaxRate(),
                    '{region:top_deposit_rub_min_amount}' => $topRubDepositCurrency->getMinAmount(),
                    '{region:top_deposit_rub_min_period}' => $topRubDepositCurrency->getMinPeriodNotation(),
                ];
            }

            /** @var Deposit $topForeignDeposit */
            $topForeignDeposit = self::queryModel(Deposit::find()->with('rates')->innerJoinWith('ratings')->where(["deposit_rating.currency_id" => Currency::USD])->orderBy('deposit_rating.value DESC')->limit(1));
            if ($topForeignDeposit) {
                $topForeignDepositCurrency = $topForeignDeposit->getDepositCurrency(Currency::USD);
                $substitutions += [
                    '{region:top_deposit_foreign_name}' => $topForeignDeposit->product_name,
                    '{region:top_deposit_foreign_min_rate}' => $topForeignDepositCurrency->getMinRate(),
                    '{region:top_deposit_foreign_max_rate}' => $topForeignDepositCurrency->getMaxRate(),
                    '{region:top_deposit_foreign_min_amount}' => $topForeignDepositCurrency->getMinAmount(),
                    '{region:top_deposit_foreign_min_period}' => $topForeignDepositCurrency->getMinPeriodNotation(),
                ];
            }

            list($substitutions['{region:deposits_count}'], $substitutions['{region:banks_count}']) =
                self::query('SELECT count(DISTINCT d.id) as "0", count(DISTINCT d.bank_id) as "1" FROM deposit_regions dr
INNER JOIN deposit d on d.id = dr.deposit_id', [], false);

            list($substitutions['{region:min_rate_rub}'], $substitutions['{region:max_rate_rub}']) =
                self::query('SELECT min(r.rate_min) as "0", max(r.rate_max) as "1" FROM deposit_regions dr
  INNER JOIN deposit_rate r on r.deposit_id = dr.deposit_id
WHERE r.currency_id = :currency', ['currency' => Currency::RUB], false);

            list($substitutions['{region:min_rate_foreign}'], $substitutions['{region:max_rate_foreign}']) =
                self::query('SELECT min(r.rate_min) as "0", max(r.rate_max) as "1" FROM deposit_regions dr
  INNER JOIN deposit_rate r on r.deposit_id = dr.deposit_id
WHERE r.currency_id != :currency', ['currency' => Currency::RUB], false);
        }

        return $substitutions;
    }

    public static function getBankSubstitutions(Bank $bank)
    {
        /** @var Deposit $topRubDeposit */
        $topRubDeposit = self::queryModel(Deposit::find()->with('rates')->innerJoinWith(['ratings','regions'])->where(["deposit.bank_id" => $bank->id, "deposit_rating.currency_id" => Currency::RUB])->orderBy('deposit_rating.value DESC')->limit(1));


        /** @var Deposit $topForeignDeposit */
        $topForeignDeposit = self::queryModel(Deposit::find()->with('rates')->innerJoinWith('ratings')->where(["deposit.bank_id" => $bank->id, "deposit_rating.currency_id" => Currency::USD])->orderBy('deposit_rating.value DESC')->limit(1));

        $substitutions = [
            '{bank:name}' => $bank->name,
            '{bank:name_genitive}' => $bank->name_genitive ?: $bank->name,
            '{bank:name_prepositional}' => $bank->name_prepositional ?: $bank->name,
            '{bank:deposits_count}' => self::query('SELECT count(*) FROM deposit
             WHERE deposit.bank_id = :bankId', ['bankId' => $bank->id]),
        ];

        if ($topRubDeposit) {
            $topRubDepositCurrency = $topRubDeposit->getDepositCurrency(Currency::RUB);
            $substitutions += [
                '{bank:top_deposit_rub_name}' => $topRubDeposit->product_name,
                '{bank:top_deposit_rub_min_rate}' => $topRubDepositCurrency->getMinRate(),
                '{bank:top_deposit_rub_max_rate}' => $topRubDepositCurrency->getMaxRate(),
                '{bank:top_deposit_rub_min_amount}' => $topRubDepositCurrency->getMinAmount(),
                '{bank:top_deposit_rub_min_period}' => $topRubDepositCurrency->getMinPeriodNotation(),
                '{bank:top_deposit_rub_rating}' => $topRubDepositCurrency->rating,
            ];
        }

        if ($topForeignDeposit) {
            $topForeignDepositCurrency = $topForeignDeposit->getDepositCurrency(Currency::USD);
            $substitutions += [
                '{bank:top_deposit_foreign_name}' => $topForeignDeposit->product_name,
                '{bank:top_deposit_foreign_min_rate}' => $topForeignDepositCurrency->getMinRate(),
                '{bank:top_deposit_foreign_max_rate}' => $topForeignDepositCurrency->getMaxRate(),
                '{bank:top_deposit_foreign_min_amount}' => $topForeignDepositCurrency->getMinAmount(),
                '{bank:top_deposit_foreign_min_period}' => $topForeignDepositCurrency->getMinPeriodNotation(),
                '{bank:top_deposit_foreign_rating}' => $topForeignDepositCurrency->rating,
            ];
        }

        list($substitutions['{bank:min_rate_rub}'], $substitutions['{bank:max_rate_rub}']) =
            self::query('SELECT min(r.rate_min) as "0", max(r.rate_max) as "1" FROM deposit d
  INNER JOIN deposit_rate r on r.deposit_id = d.id
WHERE r.currency_id = :currency and d.bank_id = :bankId', ['currency' => Currency::RUB, 'bankId' => $bank->id], false);

        list($substitutions['{bank:min_rate_foreign}'], $substitutions['{bank:max_rate_foreign}']) =
            self::query('SELECT min(r.rate_min) as "0", max(r.rate_max) as "1" FROM deposit d
  INNER JOIN deposit_rate r on r.deposit_id = d.id
WHERE r.currency_id = :currency and d.bank_id = :bankId', ['currency' => Currency::RUB, 'bankId' => $bank->id], false);


        return $substitutions;
    }

    public static function getRegionSubstitutions(Region $region, $full = false)
    {
        $substitutions = [
            '{region:name}' => $region->name,
            '{region:name_genitive}' => $region->name_genitive ?: $region->name,
            '{region:name_prepositional}' => $region->name_prepositional ?: $region->name,
        ];

        if ($full) {
            $filter = new DepositFilter();
            $filter->city_ids = [$region->id];
            $filter->currency = Currency::RUB;
            /** @var Deposit $topRubDeposit */
            $topRubDeposit = self::queryModel($filter->getQuery()->limit(1)->orderBy(['rating' => SORT_DESC]));
            if ($topRubDeposit) {
                $topRubDepositCurrency = $topRubDeposit->getDepositCurrency(Currency::RUB);
                $substitutions += [
                    '{region:top_deposit_rub_name}' => $topRubDeposit->product_name,
                    '{region:top_deposit_rub_min_rate}' => $topRubDepositCurrency->getMinRate(),
                    '{region:top_deposit_rub_max_rate}' => $topRubDepositCurrency->getMaxRate(),
                    '{region:top_deposit_rub_min_amount}' => $topRubDepositCurrency->getMinAmount(),
                    '{region:top_deposit_rub_min_period}' => $topRubDepositCurrency->getMinPeriodNotation(),
                    '{region:top_deposit_rub_rating}' => $topRubDepositCurrency->rating,
                ];
            }

            $filter->currency = Currency::USD;
            /** @var Deposit $topForeignDeposit */
            $topForeignDeposit = self::queryModel($filter->getQuery()->limit(1)->orderBy(['rating' => SORT_DESC]));
            if ($topForeignDeposit) {
                $topForeignDepositCurrency = $topForeignDeposit->getDepositCurrency(Currency::USD);
                $substitutions += [
                    '{region:top_deposit_foreign_name}' => $topForeignDeposit->product_name,
                    '{region:top_deposit_foreign_min_rate}' => $topForeignDepositCurrency->getMinRate(),
                    '{region:top_deposit_foreign_max_rate}' => $topForeignDepositCurrency->getMaxRate(),
                    '{region:top_deposit_foreign_min_amount}' => $topForeignDepositCurrency->getMinAmount(),
                    '{region:top_deposit_foreign_min_period}' => $topForeignDepositCurrency->getMinPeriodNotation(),
                    '{region:top_deposit_foreign_rating}' => $topForeignDepositCurrency->rating,
                ];
            }

            list($substitutions['{region:deposits_count}'], $substitutions['{region:banks_count}']) =
                self::query('SELECT count(DISTINCT d.id) as "0", count(DISTINCT d.bank_id) as "1" FROM deposit_regions dr
INNER JOIN deposit d on d.id = dr.deposit_id WHERE dr.region_id = :regionId', ['regionId' => $region->id], false);

            list($substitutions['{region:min_rate_rub}'], $substitutions['{region:max_rate_rub}']) =
                self::query('SELECT min(r.rate_min) as "0", max(r.rate_max) as "1" FROM deposit_regions dr
  INNER JOIN deposit_rate r on r.deposit_id = dr.deposit_id
WHERE r.currency_id = :currency and dr.region_id = :regionId', ['currency' => Currency::RUB, 'regionId' => $region->id], false);

            list($substitutions['{region:min_rate_foreign}'], $substitutions['{region:max_rate_foreign}']) =
                self::query('SELECT min(r.rate_min) as "0", max(r.rate_max) as "1" FROM deposit_regions dr
  INNER JOIN deposit_rate r on r.deposit_id = dr.deposit_id
WHERE r.currency_id != :currency and dr.region_id = :regionId', ['currency' => Currency::RUB, 'regionId' => $region->id], false);
        }

        return $substitutions;
    }

    /**
     * @param CatalogCategory $category
     * @param $regionId
     * @return array
     */
    public static function getCategorySubstitutions($category, $regionId = null)
    {
        $substitutions = [
            '{category:name}' => $category->name,
            '{category:catalog_name}' => $category->catalog->name,
        ];

        $filter = new DepositFilter();
        $filter->applyCategory($category);
        if ($regionId) {
            $filter->city_ids = [$regionId];
        }

        $query = $filter->getQuery();
        $query->orderBy([])->groupBy([])->with = [];
        $query->select(['count(DISTINCT deposit.id) as "0"', '(count(DISTINCT deposit.bank_id)) as "1"']);
        list($substitutions['{category:deposits_count}'], $substitutions['{category:banks_count}']) = self::query($query->createCommand()->getRawSql(), [], false);

        $defaultCurrency = $filter->currency;
        $filter->currency = Currency::RUB;

        /** @var Deposit $topRubDeposit */
        $topRubDeposit = self::queryModel($filter->getQuery()->limit(1)->orderBy(['rating' => SORT_DESC]));
        if ($topRubDeposit) {
            $topRubDepositCurrency = $topRubDeposit->getDepositCurrency(Currency::RUB);
            $substitutions += [
                '{category:top_deposit_rub_name}' => $topRubDeposit->product_name,
                '{category:top_deposit_rub_min_rate}' => $topRubDepositCurrency->getMinRate(),
                '{category:top_deposit_rub_max_rate}' => $topRubDepositCurrency->getMaxRate(),
                '{category:top_deposit_rub_min_amount}' => $topRubDepositCurrency->getMinAmount(),
                '{category:top_deposit_rub_min_period}' => $topRubDepositCurrency->getMinPeriodNotation(),
                '{category:top_deposit_rub_rating}' => $topRubDepositCurrency->rating,
            ];
            $query = $filter->getQuery();
            $query->orderBy([])->groupBy([]);
            $query->select('min(rate.rate_min) as "0", max(rate.rate_max) as "1"');
            list($substitutions['{category:min_rate_rub}'], $substitutions['{category:max_rate_rub}']) = self::query($query->createCommand()->getRawSql(), [], false);
        }

        $foreignCurrency = $defaultCurrency == Currency::EUR ? Currency::EUR : Currency::USD;

        $filter->currency = $foreignCurrency;
        /** @var Deposit $topForeignDeposit */
        $topForeignDeposit = self::queryModel($filter->getQuery()->limit(1)->orderBy(['rating' => SORT_DESC]));
        if ($topForeignDeposit) {
            $topForeignDepositCurrency = $topForeignDeposit->getDepositCurrency($foreignCurrency);
            $substitutions += [
                '{category:top_deposit_foreign_name}' => $topForeignDeposit->product_name,
                '{category:top_deposit_foreign_min_rate}' => $topForeignDepositCurrency->getMinRate(),
                '{category:top_deposit_foreign_max_rate}' => $topForeignDepositCurrency->getMaxRate(),
                '{category:top_deposit_foreign_min_amount}' => $topForeignDepositCurrency->getMinAmount(),
                '{category:top_deposit_foreign_min_period}' => $topForeignDepositCurrency->getMinPeriodNotation(),
                '{category:top_deposit_foreign_rating}' => $topForeignDepositCurrency->rating,
            ];
            $query = $filter->getQuery();
            $query->orderBy([])->groupBy([]);
            $query->select('min(rate.rate_min) as "0", max(rate.rate_max) as "1"');
            list($substitutions['{category:min_rate_foreign}'], $substitutions['{category:max_rate_foreign}']) = self::query($query->createCommand()->getRawSql(), [], false);
        }

        $filter->currency = $defaultCurrency;

        return $substitutions;
    }

    private static function queryModel(ActiveQuery $query)
    {
        return \Yii::$app->db->cache(function(Connection $db) use ($query) {
            return $query->one();
        });
    }

    private static function query($sql, $params = [], $scalar = true)
    {
        return \Yii::$app->db->cache(function(Connection $db) use ($sql, $params, $scalar) {
            return $scalar ? $db->createCommand($sql, $params)->queryScalar() : $db->createCommand($sql, $params)->queryOne();
        });
    }

    private static function date($date)
    {
        if (!$date) return null;
        return date('m.d.Y', strtotime($date));
    }

}