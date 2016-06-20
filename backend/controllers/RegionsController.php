<?php
namespace backend\controllers;

use backend\components\Controller;
use common\models\Region;
use common\models\TopCity;
use Yii;
use yii\web\Response;

/**
 * Api controller
 */
class RegionsController extends Controller
{

    /**
     * Filters list of banks
     * @param $q
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionFilter($q)
    {
        Yii::$app->response->format=Response::FORMAT_JSON;
        /** @var Region[] $regions */
        $regions = Region::find()->with('parent')
            ->select(['region.name', 'region.id', 'region.parent_id'])
            ->where(['is_city' => true])
            ->andWhere("name ilike :q", ['q' => pg_escape_string($q)."%"])
            ->orderBy(['is_regional_center'  => SORT_DESC, 'name' => SORT_ASC])->limit(20)->all();
        $data = [];
        foreach($regions as $region) {
            $data[] = ['name' => $region->getFullName(), 'id' => $region->id];
        }
        return $data;
    }

    public function actionIndex()
    {
        $model = new TopCity();
        $topCities = Region::getTopCities();
        return $this->render('index', compact('topCities', 'model'));
    }

    public function actionTree($key = 1)
    {
        /** @var Region[] $regions */
        $regions = Region::find()->where('parent_id = :key')->orderBy('sort_number')->limit(10)->params(['key' => $key])->all();
        $this->renderJSON($this->getChildren($regions));
    }

    /**
     * @param Region[] $regions
     * @return array
     */
    public function getChildren(array $regions)
    {
        $data = [];
        foreach($regions as $region) {
            $lazy = !$region->is_city;
            $item = ['key' => $region->id,
                'title' => $region->name.($region->is_active ? '' : ' (неактивный)'),
                'isFolder' => true,
                'icon' => false,
                'expanded' => !$lazy,
                'lazy' => $lazy,
            ];
            if (!$lazy) {
                $item['children'] = $this->getChildren($region->children);
            }
            $data[] = $item;
        }
        return $data;
    }

    public function actionSave()
    {
        if (!$topIds = Yii::$app->request->post('top_ids')) {
            $this->renderJSON(false, 'request incorrect');
        }
        $topIds = array_unique($topIds);
        $rows = [];
        foreach($topIds as $key => $topId) {
            $rows[] = [$key + 1, $topId];
        }
        $cnt = TopCity::getDb()->transaction(function($db) use ($rows) {
            TopCity::deleteAll();
            return \Yii::$app->db->createCommand()->batchInsert((new TopCity())->tableName(), ['id', 'region_id'], $rows)->execute();
        });

        $this->renderJSON(true, "$cnt top cities saved");
    }
}
