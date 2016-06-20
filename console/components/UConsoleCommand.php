<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/20/15
 * Time: 5:36 PM
 */

namespace console\components;


use yii\base\Exception;
use yii\console\Controller;
use yii\db\ActiveRecord;
use yii\log\Logger;

class UConsoleCommand  extends Controller
{
    public $startTime;
    public $time;

    public function actionIndex()
    {
        echo $this->getHelp();
    }
    public function beforeAction($action)
    {
        $this->startTime = $this->time = microtime(true);
        return parent::beforeAction($action);
    }


    public function log($message, $level = Logger::LEVEL_INFO)
    {
        print_r($message);
        echo "\n";
        \Yii::getLogger()->log(print_r($message, true), $level, 'command.'.$this->getUniqueId());
    }

    protected function requireSave(ActiveRecord $model)
    {
        if (!$model->save()) {
            $this->logSaveError($model);
        }
    }

    protected function logSaveError(ActiveRecord $model)
    {
        if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        } else {
            $backtrace = debug_backtrace();
        }

        if (empty($_SERVER['argv'])) {
            $request = \Yii::$app->request->getUrl();
        } else {
            $request = implode(' ', $_SERVER['argv']);
        }

        $class = get_class($model);
        $data = array(
            "$class save error",
            'attrs' => $model->getAttributes(),
            'errors' => $model->getErrors(),
            'request' => $request,
            'file' => $backtrace[0]['file'],
            'line' => $backtrace[0]['line'],
        );
        self::log($data, Logger::LEVEL_ERROR);
        throw new Exception(reset($data));
    }

    public function startProfile()
    {
        $this->time = microtime(true);
    }

    public function profile($message, $level = Logger::LEVEL_INFO)
    {
        $time = round(microtime(true) - $this->time, 2);
        print_r($message);
        echo " ($time s)\n";
        \Yii::getLogger()->log($message, $level, 'command.'.$this->getUniqueId());
        $this->startProfile();
    }

    public function endProfile($level = Logger::LEVEL_INFO)
    {
        $message = 'Done';
        $time = round(microtime(true) - $this->startTime, 2);
        print_r($message);
        echo " ($time s)\n";
        \Yii::getLogger()->log($message, $level, 'command.'.$this->getUniqueId());
    }

    public function delimeter()
    {
        $this->log("\n\n====================================================================================================\n");
    }

    public function emptyLine()
    {
        $this->log("");
    }
}