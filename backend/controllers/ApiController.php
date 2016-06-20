<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\ApiForm;
use common\components\ConsoleRunner;
use common\models\ApiLog;
use Yii;
use yii\filters\AccessControl;

/**
 * Api controller
 */
class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

        /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $log = new ApiLog();
        $log->setScenario('search');
        $log->load(Yii::$app->request->queryParams);

        $model = new ApiForm();

        return $this->render('index', ['searchModel' => $log, 'model' => $model]);
    }

    public function actionRun()
    {
        $model = new ApiForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** @var ConsoleRunner $consoleRunner */
            $consoleRunner = Yii::$app->consoleRunner;
            foreach($model->types as $type) {
                $consoleRunner->run("api/sync $type", "@runtime/logs/$type.log");
            }
            sleep(2);
        }
    }

    public function actionStatus($ids)
    {
        /** @var ApiLog[] $logs */
        $logs = ApiLog::findAll(['id' => explode(',', $ids)]);
        $data = [];
        foreach($logs as $log) {
            $data[$log->id] = $log->getLiveStatus();
        }
        $this->renderJSON(['success' => true, 'data' => $data]);
    }
}
