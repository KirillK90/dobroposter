<?php
namespace backend\controllers;

use backend\components\Controller;
use common\enums\PageType;
use common\models\BannerPlace;
use common\models\PageBanners;
use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 *  PageBanners controller controller
 */
class PageBannersController extends Controller
{
    public function actionIndex()
    {
        $models = PageBanners::find()->joinWith('bannerPlace')->all();
        $models = ArrayHelper::map($models, 'bannerPlace.place', function($model){return $model;}, 'page');

        return $this->render('index', [
            'dataProvider' => new ArrayDataProvider(['allModels' => PageType::getBannersList(), 'pagination' => false]),
            'models' => $models,
        ]);
    }

    protected function findBannerPlace($place)
    {
        $attributes = ['place' => $place];
        if (!$place = BannerPlace::findOne($attributes)) {
            $place = new BannerPlace();
            $place->setAttributes($attributes);
            if (!$place->validate(['place'])) {
                throw new NotFoundHttpException('Место не найдено');
            }
        }
        return $place;
    }

    /**
     * @param BannerPlace $place
     * @return PageBanners[]
     */
    protected function findPageBanners($place)
    {
        $banners = PageBanners::find()->joinWith(['bannerPlace' => function(Query $q) use ($place) {
            $q->andWhere(['banner_place.place' => $place->place]);
        }], 'RIGHT JOIN')->indexBy('page')->all();
        foreach (PageType::getValues() as $page) {
            if (!isset($banners[$page])) {
                $banners[$page] = new PageBanners([
                    'bannerPlace' => $place,
                    'place' => $place->place,
                    'page' => $page,
                    'enabled' => true,
                    'default' => true,
                ]);
            }
        }
        return $banners;
    }

    public function actionUpdate($place)
    {
        $post = Yii::$app->request->post();
        $place = $this->findBannerPlace($place);
        $success_count = 0;
        $total_count = 0;
        if ($place->load($post) && $place->save()) {
            $success[] = ACTION_UPDATE_SUCCESS;
        } else {
            $error[] = ACTION_VALIDATE_ERROR;
        }

        $banners = $this->findPageBanners($place);

        if (Model::loadMultiple($banners, $post)) {
//            $errors = ActiveForm::validateMultiple($banners);
//            if (Yii::$app->request->isAjax) {
//                Yii::$app->response->format = Response::FORMAT_JSON;
//                return $errors;
//            }
            foreach ($banners as $banner) {
                if ($banner->save()) {
                    $success = ACTION_UPDATE_SUCCESS_MULTIPLE;
                    $success_count++;
                } else {
                    $error[] = ACTION_VALIDATE_ERROR . var_export($banner->errors, true);
                }
                $total_count++;
            }
            if (isset($success)) {
                $this->setFlash('success', sprintf($success.ACTION_APPEND_OUTOF_MULTIPLE, $success_count, $total_count));
            }
            if (isset($error)) {
                $this->setFlash('error', $error);
            }
        }

        return $this->render('update', [
            'pageBanners' => $banners,
            'bannerPlace' => $place,
            'bannerItems' => $place->getDefaultBannerCandidates()
                ->select(['name', 'id'])
                ->indexBy('id')
                ->column(),
        ]);
    }
}
