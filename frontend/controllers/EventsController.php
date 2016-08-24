<?php

namespace frontend\controllers;

use common\enums\ArticleStatus;
use common\enums\ArticleType;
use common\models\Article;
use common\models\Event;
use frontend\components\Controller;
use frontend\models\Analytics;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * ArticlesController implements the CRUD actions for Deposit model.
 */
class EventsController extends Controller
{
    public function actionIndex($type)
    {
        if ($type==ArticleType::PAGE || !ArticleType::hasValue($type)) {
            throw new NotFoundHttpException("Страница не найдена");
        }
        $query = Article::find()
            ->select(['id', 'slug', 'type', 'title', 'image_src', 'published_at', 'announcement', 'is_primary'])
            ->where(['status' => ArticleStatus::PUBLISHED, 'type' => $type])
            ->orderBy(['published_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider(['query' => $query,
            'pagination' => [
                'defaultPageSize' => 25,
        ]]);


        $topArticles = [];
        if (Yii::$app->request->getQueryParam('page', 1) == 1) {
            $models = $dataProvider->getModels();
            $topArticles = array_slice($models, 0, 6);
            $dataProvider->setModels(array_slice($models, 6));
        }

        return $this->render('index', ['topArticles' => $topArticles, 'type' => $type, 'dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single News model.
     * @param $slug
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($slug, $preview=false)
    {
        /** @var Event $model */
        $model = Event::find()->where(['slug' => $slug])->one();
        if (!$model) {
            throw new NotFoundHttpException("Событие не найдено");
        } elseif ($preview) {
            $model->preview = true;
        } elseif (!$model->published()) {
            if (!$this->getView()->isPrivilegedUser()) {
                throw new NotFoundHttpException('Материал снят с публикации');
            }
        } else {
            $model->updateCounters(['views_count' => 1]);
        }
        return $this->render('view', compact('model'));
    }

    public function actionBest($currency, $period)
    {
        return $this->renderAjax("_bestToday", Analytics::getBestToday($currency, $period));
    }

    public function actionChart($period)
    {
        return $this->renderAjax("_chart", ['series' => Analytics::getChartData($period)]);
    }

}