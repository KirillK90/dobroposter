<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class CacheController extends Controller
{

    public function actionFlush()
    {
        Yii::$app->cache->flush();
        echo "Cache flushed. ";
        // clear the cache of all loaded tables\
        Yii::$app->db->schema->refresh();
        if(isset(Yii::$app->sphinx)) {
            Yii::$app->sphinx->schema->refresh();
        }
        echo "Schema cache flushed";
    }
}
