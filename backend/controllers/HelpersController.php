<?php
namespace backend\controllers;

use backend\components\Controller;
use common\models\Dictionary;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Api controller
 */
class HelpersController extends Controller
{

    public function actionIndex()
    {
        $dictionaries = Dictionary::find()->all();
        $helpers = ArrayHelper::map($dictionaries, 'id', 'name', 'type');
        return $this->render('index', compact('helpers'));
    }

}
