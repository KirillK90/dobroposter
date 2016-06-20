<?php
namespace backend\controllers;

use backend\components\Controller;
use common\components\helpers\HDev;
use common\enums\PageType;
use common\enums\SeoCustomParam;
use common\enums\SeoParam;
use common\models\PageSeo;
use common\models\Seo;
use frontend\components\Response;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;

/**
 * Seo controller
 */
class SeoController extends Controller
{

    public function actionIndex()
    {
        /** @var Seo[] $seoModels */
        $seoModels = PageSeo::find()->with('seo')->where(['param_id' => null])->indexBy('page')->all();
        $gridData = [];
        foreach(PageType::getList() as $page => $pageName) {
            $row = ['page' => $page];
            foreach(SeoParam::getValues() as $param) {
                $row[$param] = (bool) ArrayHelper::getValue($seoModels, "$page.seo.$param");
            }
            $gridData[] = $row;
        }

        $dataProvider = new ArrayDataProvider(['allModels' => $gridData, 'pagination' => false]);

        return $this->render('index', compact('dataProvider'));
    }

    public function actionCreate($page, $param_id)
    {
        if (!PageType::hasValue($page)) {
            throw new NotFoundHttpException("Страница $page не найдена");
        }
        if (!SeoCustomParam::getParam($page)) {
            throw new NotFoundHttpException("Для страницы $page не доступна кастомизация");
        }

        if (PageSeo::findOne(['page' => $page, 'param_id' => $param_id])) {
            return $this->redirect(['update', 'page' => $page, 'param_id' => $param_id]);
        }

        $model = new Seo();
        $model->setParamModel($page, $param_id);

        /** @var Seo $model */
        $defaultSeo = Seo::find()->joinWith('pageSeo')->where(['page' => $page, 'param_id' => null])->one();
        $model->loadFromDefault($defaultSeo);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->getDb()->transaction(function() use ($model) {
                    if (!$model->save()) {
                        HDev::logSaveError($model, true);
                    }
                    $pageSeo = new PageSeo([
                        'page' => $model->page,
                        'param_id' => $model->param_id,
                        'param_name' => SeoCustomParam::getParam($model->page),
                        'seo_id' => $model->id,
                    ]);
                    if (!$pageSeo->save()) {
                        HDev::logSaveError($pageSeo, true);
                    }
                });

                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
                return $this->redirect(['update', 'page' => $page, 'param_id' => $param_id]);

            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        $model->setExampleSubstitutions();

        return $this->render('update_custom', ['model' => $model]);
    }

    public function actionUpdate($page, $param_id)
    {
        if (!PageType::hasValue($page)) {
            throw new NotFoundHttpException("Страница $page не найдена");
        }

        /** @var Seo $model */
        $model = Seo::find()->joinWith('pageSeo')->where(['page' => $page, 'param_id' => $param_id])->one();

        if (!$model) {
            return $this->redirect(['/seo/create', 'page' => $page, 'param_id' => $param_id]);
        }

        $model->setParamModel($page, $param_id);
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        $model->setExampleSubstitutions();

        return $this->render('update_custom', ['model' => $model]);
    }

    public function actionDelete($page, $param_id)
    {
        /** @var Seo $model */
        $model = Seo::find()->joinWith('pageSeo')->where(['page' => $page, 'param_id' => $param_id])->one();
        if ($model && $model->delete()) {
            $this->setFlash('success', ACTION_DELETE_SUCCESS);
        } else {
            $this->setFlash('error', "Не удалось удалить");
        }
        $this->redirect(['/seo/update-default', 'page' => $page]);
    }

    public function actionUpdateDefault($page)
    {
        if (!PageType::hasValue($page)) {
            throw new NotFoundHttpException("Страница $page не найдена");
        }

        /** @var Seo $model */
        $model = Seo::find()->joinWith('pageSeo')->where(['page' => $page, 'param_id' => null])->one();

        if (!$model) {
            $model = new Seo();
            if (!$model->save()) {
                HDev::logSaveError($model, true);
            }
            $pageSeo = new PageSeo([
                'page' => $page,
                'seo_id' => $model->id,
            ]);
            if (!$pageSeo->save()) {
                HDev::logSaveError($pageSeo, true);
            }
        }

        $model->page = $page;
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        $model->setExampleSubstitutions();

        $newPage = new PageSeo();
        $newPage->page = $page;
        if ($newPage->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($newPage->param_id) {
                return $this->redirect(Url::to(['/seo/create', 'page' => $page, 'param_id' => $newPage->param_id]));
            } else {
                $newPage->validate();
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        /** @var PageSeo[] $customPages */
        $customPages = PageSeo::find()->where(['page' => $page])->andWhere('param_id is not null')->indexBy('param_id')->all();

        return $this->render('update_default', ['model' => $model, 'customPages' => $customPages, 'newPage' => $newPage]);
    }

}
