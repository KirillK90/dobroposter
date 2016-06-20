<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class CacheController extends Controller
{

    public function actionFlush()
    {
        // clear the cache of all loaded tables\
        Yii::$app->db->schema->refresh();
        echo "Schema cache flushed";
    }
}
