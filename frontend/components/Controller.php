<?php

namespace frontend\components;

use common\components\helpers\HDev;
use common\components\helpers\SubstitutionsHelper;
use common\enums\PageType;
use common\enums\SeoCustomParam;
use common\enums\SessionVar;
use common\models\Alias;
use common\models\DepositFilter;
use common\models\FormData;
use common\models\PageBanners;
use common\models\Region;
use frontend\models\Seo;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * DepositController implements the CRUD actions for Deposit model.
 * @property string $pageType  тип страницы
 * @method View getView()
 */
class Controller extends \common\components\Controller
{

    public function getPageType()
    {
        $pageType = PageType::MAIN;
        $action = $this->action;
        switch($this->id) {
            case 'site':
                if ($this->action->id == 'index') {
                    $pageType = PageType::MAIN;
                } elseif ($action->id == 'page') {
                    $pageType = Yii::$app->request->get('pageType');
                    $this->view->params['staticPage'] = $pageType;
                } elseif ($action->id == 'profile') {
                    $pageType =PageType::PROFILE;
                } elseif ($action->id == 'search') {
                    $pageType =PageType::SEARCH;
                } elseif ($action->id == 'signup') {
                    $pageType =PageType::SIGNUP;
                }
                break;
            case 'regions':
                $pageType = PageType::REGIONS;
                break;
            case 'banks':
                if ($this->action->id == 'index') {
                    $pageType = PageType::BANKS;
                } elseif ($action->id == 'view') {
                    $pageType = PageType::BANK;
                } elseif ($action->id == 'category') {
                    $pageType = PageType::BANK_CATEGORY;
                }
                break;
            case 'deposits':
                if ($action->id == 'view') {
                    $pageType = PageType::DEPOSIT;
                } elseif ($action->id == 'search') {
                    $pageType = PageType::DEPOSIT_SEARCH;
                }
                break;
            case 'catalogs':
                if ($action->id == 'category') {
                    $pageType = PageType::CATEGORY;
                } elseif ($action->id == 'index') {
                    $pageType = PageType::KINDS;
                } elseif ($action->id == 'bank') {
                    $pageType = PageType::BANK_KINDS;
                }
                break;
            case 'articles':
                if ($action->id == 'view') {
                    $pageType = PageType::ARTICLE;
                } elseif ($action->id == 'index') {
                    $pageType = Yii::$app->request->get('type');
                }
                break;
        }
        return $pageType;
    }

    public function beforeAction($action)
    {
        if (!Yii::$app->request->isAjax) {
            $this->setRegion();
            $this->setDefaultSeo();
            $this->setBanners();
        }
        return parent::beforeAction($action);
    }

    private function setRegion()
    {
        $regionSlug = Yii::$app->request->get('regionSlug');
        $currentRegionId = Yii::$app->session->get(SessionVar::CURRENT_REGION);
        $currentRegion = null;
        if ($regionSlug) {
            if ($regionSlug == Region::RUSSIA && $currentRegionId) {
                unset(Yii::$app->session[SessionVar::CURRENT_REGION]);
            } else {
                $newRegion = Alias::findRegion($regionSlug);
                if ($newRegion && $newRegion->id != $currentRegionId) {
                    Yii::$app->session->set(SessionVar::CURRENT_REGION, $newRegion->id);
                }
                $currentRegion = $newRegion;
            }
        } elseif ($currentRegionId) {
            $currentRegion = Region::findOne($currentRegionId);
        }

        if ($currentRegion) {
            $this->view->params['region'] = $currentRegion;
        }
    }

    private function setDefaultSeo()
    {
        //Для статей модель для сео подставляем из экшена
        if ($this->pageType == PageType::ARTICLE) {
            return;
        }
        /** @var Seo $defaultSeo */
        $defaultSeo = Seo::find()
            ->innerJoinWith('pageSeo', false)
            ->where(['page_seo.page' => $this->pageType, 'page_seo.param_id' => null])
            ->one();
        if ($defaultSeo) {
            $defaultSeo->setSubstitutions(SubstitutionsHelper::getCommonSubstitutions());
            $defaultSeo->page = $this->pageType;
            $defaultSeo->region = $this->getView()->getRegion();
            if ($region = $this->getView()->getRegion()) {
                $defaultSeo->setSubstitutions(SubstitutionsHelper::getRegionSubstitutions($region, $this->pageType == PageType::DEPOSIT_SEARCH));
            }
            else {
                $defaultSeo->setSubstitutions(SubstitutionsHelper::getRussiaSubstitutions($this->pageType == PageType::DEPOSIT_SEARCH));
            }
        }

        $this->view->params['seo'] = $defaultSeo;
    }

    public function setCustomSeo($paramId)
    {
        if (!SeoCustomParam::getParam($this->getPageType())) {
            return;
        }
        /** @var Seo $customSeo */
        $customSeo = Seo::find()
            ->innerJoinWith('pageSeo', false)
            ->where(['page_seo.page' => $this->pageType, 'page_seo.param_id' => $paramId])
            ->one();
        if ($customSeo && $seo = ArrayHelper::getValue($this->view->params,'seo')) {
            $seo->customSeo = $customSeo;
        }
    }

    private function setBanners()
    {
        /** @var PageBanners[] $pageBanners */
        $pageBanners = PageBanners::find()
            ->with('bannerPlace', 'bannerPlace.defaultBanner')
            ->where(['page' => PageType::getBannerPageType($this->pageType)])
            ->indexBy('place')->all();

        if (!$pageBanners && $this->pageType != PageType::MAIN) {
            $pageBanners = PageBanners::find()
                ->with('bannerPlace', 'bannerPlace.defaultBanner')
                ->where(['page' => PageType::MAIN])
                ->indexBy('place')->all();
        }

        foreach($pageBanners as $place => $pageBanner) {
            if ($pageBanner && $pageBanner->enabled) {
                if ($pageBanner->default) {
                    $this->view->params['banners'][$place] = $pageBanner->bannerPlace->defaultBanner;
                } elseif ($pageBanner->overrideBanner) {
                    $this->view->params['banners'][$place] = $pageBanner->overrideBanner;
                }
            }
        }
    }

    protected function setSeoSubstitutions(array $substitutions)
    {
        /** @var Seo $seo */
        if ($seo = ArrayHelper::getValue($this->view->params, 'seo')) {
            $seo->setSubstitutions($substitutions);
        }
    }

    protected function getFilter($hash = null, $withProf = false)
    {
        $filter = new DepositFilter();
        if ($filter->load(Yii::$app->request->post())) {
            $hash = FormData::saveForm($filter);
            $urlParams = array_merge(
                ['search'],
                Yii::$app->request->get(),
                ['q' => $hash]
            ) ;
            $this->redirect($urlParams);
        } elseif ($hash) {
            $data = FormData::getData($hash, $filter->className());
            $filter->attributes = $data;
        } else {
            $filter->loadDefaults($withProf);
        }

        if (!$filter->validate()) {
            HDev::logSaveError($filter);
            $filter= new DepositFilter();
            $filter->loadDefaults($withProf);
        }

        return $filter;
    }

}