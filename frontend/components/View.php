<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 03.07.15
 * Time: 20:39
 */

namespace frontend\components;

use common\components\helpers\HStrings;
use common\enums\Currency;
use common\enums\SeoParam;
use common\models\Bank;
use common\models\Banner;
use common\models\Region;
use common\models\Slider;
use frontend\models\Seo;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class View
 * @package frontend\components
 *
 * @property string $regionName  @readonly
 */
class View extends \common\components\View
{

    public function renderBlock($block)
    {
        return ArrayHelper::getValue($this->blocks, $block);
    }

    public function getSeo($param, $default = null)
    {
        //Статья или другая модель откуда брать сео параметры
        if ($seoModel = ArrayHelper::getValue($this->params, "seoModel")) {
            return ArrayHelper::getValue($this->params, $param);
        }
        /** @var Seo $seo */
        if (!($seo = ArrayHelper::getValue($this->params, "seo")) || !$value = $seo->getParam($param)) {
            $value = $default;
        }
        return $value;
    }

    /**
     * @param $place
     * @return Banner
     */
    public function getBanner($place)
    {
        return ArrayHelper::getValue($this->params, "banners.$place");
    }

    public function getRegionName()
    {
        return ArrayHelper::getValue($this->params, "region.name", Region::RUSSIA_NAME);
    }

    public function isRegionSet()
    {
        return isset($this->params['region']);
    }

    public function isCurrentRegion($regionId)
    {
        return $regionId === ArrayHelper::getValue($this->params, "region.id");
    }

    public function getRegionSlug()
    {
        if ($this->isRegionSet()) {
            return $this->getRegion()->slug;
        }
        return '';
    }

    public function getRegionsUrl()
    {
        return ArrayHelper::getValue($this->params, 'regionsUrl',
            Url::to(['/regions/index', 'back_url' => Yii::$app->request->getAbsoluteUrl()]));
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return ArrayHelper::getValue($this->params, 'region');
    }

    public function getDefaultTitle()
    {
        $title = '';
        if ($h1 = $this->getSeo(SeoParam::H1)) {
            $title .= "$h1 - ";
        }
        $title .= Yii::$app->name;
        return $title;
    }

    public function isStaticPage()
    {
        return ArrayHelper::getValue($this->params, 'staticPage');
    }

    public function getCurrency($default = Currency::RUB)
    {
        $currency = Yii::$app->request->get('curr', $default);
        if (!in_array($currency, Currency::getValues())) {
            $currency = $default;
        }
        return $currency;
    }

    public function getPeriod($default = null)
    {
        $period = Yii::$app->request->get('period', $default);
        if ($period && !HStrings::isPositiveNumber($period)) {
            $period = $default;
        }
        return $period;
    }

    public function getAmount($default = null)
    {
        $amount = Yii::$app->request->get('amount', $default);
        if ($amount && !HStrings::isPositiveNumber($amount)) {
            $amount = $default;
        }
        return $amount;
    }

    public function beforeRender($viewFile, $params)
    {
        if($flashMessages = Yii::$app->session->getAllFlashes()){
            $this->registerGlobals(['flashMessages'=>array_values($flashMessages)]);
        }
        return parent::beforeRender($viewFile, $params);
    }

    public function isPrivilegedUser()
    {
        return (boolean) ArrayHelper::getValue(Yii::$app->user->getIdentity(), 'role');
    }

    public function showBestTabs()
    {
        return ArrayHelper::getValue($this->params, 'showBestTabs', true);
    }

    public function getLogoUrl()
    {
        return Yii::getAlias('@static/images/logo.png');
    }

    public function setBadge($string)
    {
        $this->params['badges'][] = $string;
    }

    public function renderBadge()
    {
        if (isset($this->params['badges'])) {
            $badges = [];
            foreach($this->params['badges'] as $badge) {
                $badges[] = "<div class='badge'>{$badge}</div>";
            }
            return implode("<br>\n", $badges);
        } else {
            return '';
        }
    }

    public function showRatingBlock()
    {
        return ArrayHelper::getValue($this->params, 'showRatingInfo', false);
    }

    /**
     * @return Slider
     */
    public function getMainSlider()
    {
        if (!isset($this->params['mainSlider'])) {
            $this->params['mainSlider'] = Slider::find()->where(['enabled' => true])->one();
        }
        return $this->params['mainSlider'];
    }

    public function isSubscribed()
    {
        if ($user = $this->getUser()) {
            return $user->is_subscribed;
        } else {
            return Yii::$app->request->cookies->getValue('email_subscribed', false);
        }
    }

    public function getBank()
    {
        return ArrayHelper::getValue($this->params, 'bank');
    }

    public function setBank(Bank $bank)
    {
        return $this->params['bank'] = $bank;
    }

    public function showCatalogs()
    {
        return ArrayHelper::getValue($this->params, 'show_catalogs', true);
    }

    public function getBottomSlider()
    {
        return ArrayHelper::getValue($this->params, 'bottomSlider');
    }
}