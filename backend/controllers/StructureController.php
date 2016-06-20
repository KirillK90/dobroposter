<?php

namespace backend\controllers;

use backend\components\Controller;
use backend\models\CatalogForm;
use backend\models\CategoryForm;
use common\enums\CatalogType;
use common\enums\UserRole;
use common\models\CatalogCategory;
use common\models\Deposit;
use common\models\DepositCatalog;
use common\models\DepositFilter;
use common\models\FormData;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * DepositsController implements the CRUD actions for Deposit model.
 */
class StructureController extends Controller
{

    public function beforeAction($action)
    {
        if ($this->action->id == 'search2') {
            $this->allowedRoles[] = UserRole::EDITOR;
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * Filters list of banks
     * @param $q
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionSearch($q=null)
    {
        Yii::$app->response->format=Response::FORMAT_JSON;
        $query = CatalogCategory::find()->select(['catalog_category.name', 'catalog_category.id', 'deposit_catalog.name as catalog_name'])
            ->innerJoinWith(['catalog' => function (Query  $query) {
                $query->select('deposit_catalog.name');
            }], false);
        if ($q) {
            $query->andWhere(['ilike', 'catalog_category.name', $q]);
        }

        $rawData = $query->orderBy('catalog_category.name')->limit(20)->asArray()->all();
        $data = [];
        foreach ($rawData as $row) {
            $data[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'group' => $row['catalog_name'],
            ];
        }
        return $data;
    }

    /**
     * Filters list of banks
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionSearch2()
    {
        $request = Yii::$app->request;
        $results = ['data' => [], 'page' => 1, 'total' => 0];
        $query   = $request->post('query');
        $page    = $request->post('page', 1);

        $currentPage = 0;
        if (!empty($page) && is_numeric($page) && $page > 0) {
            $currentPage = $page - 1;
        }

        if (empty($query)) {
            $queryObject = CatalogCategory::find();
        }
        else {
            $queryObject = CatalogCategory::find()->where(['ilike', 'name', $query]);
        }

        $queryObject->andWhere(['type' => CatalogType::CUSTOM]);
        $queryObject->orderBy('name asc');

        $provider = new ActiveDataProvider([
            'query' => $queryObject,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $provider->getPagination()->setPage($currentPage);

        foreach ($provider->getModels() as $data) {
            /** @var CatalogCategory $data */
            $results['data'][] = [
                'id'    => $data->id,
                'mark'  => 0,
                'value' => $data->name,
            ];
        }

        $results['page']  = $provider->getPagination()->getPage() + 1;
        $results['total'] = $provider->getPagination()->getPageCount();

        return Json::encode($results);
    }

    /**
     * Filters list of banks
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionSearchDeposits($id = null)
    {
        if (!$id) {
            return Json::encode(['data' => []]);
        }
        $category = $this->findCategory($id);

        $request = Yii::$app->request;
        $results = ['data' => [], 'page' => 1, 'total' => 0];
        $query   = $request->post('query');
        $page    = $request->post('page', 1);

        $currentPage = 0;
        if (!empty($page) && is_numeric($page) && $page > 0) {
            $currentPage = $page - 1;
        }

        $filter = new DepositFilter();
        $filter->applyCategory($category);
        $provider = $filter->searchAdmin(new Deposit(['product_name' => $query]));
        $provider->pagination->pageSize = 10;
        $provider->pagination->setPage($currentPage);

        foreach ($provider->getModels() as $data) {
            /** @var Deposit $data */
            $results['data'][] = [
                'id'    => $data->id,
                'mark'  => 0,
                'value' => $data->product_name,
            ];
        }

        $results['page']  = $provider->getPagination()->getPage() + 1;
        $results['total'] = $provider->getPagination()->getPageCount();

        return Json::encode($results);
    }

    public function actionIndex()
    {
        $catalogForm = new CatalogForm();
        if ($catalogForm->load(Yii::$app->request->post())) {
            $catalogForm->type = CatalogType::CUSTOM;
            if ($catalogForm->save()) {
                $this->setFlash('success', "Новый каталог успешно добавлен");
            }
        }

        /** @var DepositCatalog[] $catalogs */
        $catalogs = DepositCatalog::find()->all();
        return $this->render('index', compact('catalogs', 'catalogForm'));
    }

    public function actionSavePopular()
    {
        $ids = Yii::$app->request->post('ids');
        Yii::$app->response->format = Response::FORMAT_JSON;

        CatalogCategory::getDb()->transaction(function($db) use ($ids) {
            CatalogCategory::updateAll(['is_popular' => false]);
            CatalogCategory::updateAll(['is_popular' => true], ['id' => $ids]);
        });

        return ['result' => true];
    }

    public function actionSaveOrder()
    {
        $ids = Yii::$app->request->post('ids');
        Yii::$app->response->format = Response::FORMAT_JSON;

        CatalogCategory::getDb()->transaction(function($db) use ($ids) {
            $catalogs = CatalogCategory::find()->where(['id' => $ids])->indexBy('id')->select(['id', 'order'])->all();
            foreach ($ids as $order => $id) {
                $catalogs[$id]->updateAttributes(['order' => $order]);
            }
        });

        return ['result' => true];
    }



    public function actionNewCategory($id, $hash = null)
    {
        /** @var DepositCatalog $catalog */
        $catalog = DepositCatalog::findOne($id);
        if (!$catalog || $catalog->type != CatalogType::CUSTOM) {
            throw new NotFoundHttpException("Каталог не найден");
        }
        $filter = $this->getFilter($hash);
        $categoryForm = new CategoryForm();
        $categoryForm->catalog_id = $catalog->id;
        if ($categoryForm->load(Yii::$app->request->post())) {
            if ($categoryForm->save()) {
                $this->setFlash('success', "Подборка успешно добавлена");
                $this->redirect(['/structure/category', 'id' => $categoryForm->id]);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('create', ['filterOthers' => $filter, 'model' => $categoryForm]);
    }

    public function actionUpdateCategory($id, $hash = null)
    {
        /** @var CategoryForm $categoryForm */
        $categoryForm = CategoryForm::findOne($id);
        if (!$categoryForm) {
            throw new NotFoundHttpException("Категория не найдена");
        }

        if ($categoryForm->load(Yii::$app->request->post())) {
            if ($categoryForm->save()) {
                $categoryForm->refresh();
                $this->setFlash('success', "Подборка успешно добавлена");
            }
        }

        if ($hash) {
            $filterOthers = $this->getFilter($hash);
        } else {
            $filterOthers = new DepositFilter();
            if ($categoryForm->dynamic) {

            }
        }

        if ($categoryForm->isDynamic()) {
            $filterOthers->applyCategory($categoryForm);
        } else {
            $filterOthers->except_ids = $categoryForm->getDepositIds();
        }

        $filterChecked = new DepositFilter();
        $filterChecked->ids = $categoryForm->getDepositIds();

        return $this->render('update', ['filterOthers' => $filterOthers, 'filterChecked' => $filterChecked, 'model' => $categoryForm]);
    }

    public function actionDeleteCategory($id)
    {
        /** @var CatalogCategory $category */
        $category = CatalogCategory::findOne($id);
        if (!$category) {
            throw new NotFoundHttpException("Категория не найдена");
        }
        if ($category->delete()) {
            $this->setFlash('warning', "Категория {$category->name} удалена");
            return $this->redirect(['index']);
        } else {
            $this->setFlash('error', "Не удалось удалить категорию {$category->name}");
            return $this->goBack();
        }
    }

    /**
     * @param $id
     * @return CatalogCategory
     * @throws NotFoundHttpException
     */
    public function findCategory($id)
    {
        $category = CatalogCategory::findOne($id);
        if (!$category) {
            throw new NotFoundHttpException("Категория не найдена");
        }
        return $category;
    }

    public function actionCategory($id)
    {
        $this->redirect(['update-category', 'id' => $id]);
        $category = $this->findCategory($id);

        if ($category->load(Yii::$app->request->post())) {
            if ($category->save()) {
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
            } else {
                $this->setFlash('danger', ACTION_VALIDATE_ERROR);
            }
        }

        $filter = $this->getFilter($category->form_hash);
        $filter->applyCategory($category);

        return $this->render('category', ['category' => $category, 'filter' => $filter]);
    }

    private function getFilter($hash = null)
    {
        $filter = new DepositFilter();
        if ($filter->load(Yii::$app->request->post())) {
            $hash = FormData::saveForm($filter);
            $urlParams = array_merge(
                ['filter'],
                Yii::$app->request->get(),
                ['hash' => $hash]
            ) ;
            $this->redirect($urlParams);
        } elseif ($hash) {
            $data = FormData::getData($hash, $filter->className());
            $filter->attributes = $data;
        }

        return $filter;
    }

    public function actionGetHash()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $filter = new DepositFilter();
        if ($filter->load(Yii::$app->request->post())) {
            return ['success' => true, 'hash' => FormData::saveForm($filter)];
        }
        return ['success' => false, 'message' => 'NO POST'];
    }
}
