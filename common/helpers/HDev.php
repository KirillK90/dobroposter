<?php 

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\VarDumper;
use yii\log\Logger;

/**
 * Класс создан для обеспечения вспомогательными методами для отладки
 */
class HDev
{
    public static $startTime;
    public static $time;
	
	public static function trace($obj)
	{
		echo "<pre>";
        VarDumper::dump($obj);
		echo "</pre>";
	}

	public static function silent($obj,$append=false){
		if($append){
			file_put_contents('pol_dump.txt',print_r($obj,1)."\n",FILE_APPEND);
		}else{
			file_put_contents('pol_dump.txt',print_r($obj,1)."\n");
		}
	}
    public static function profile($message, $level) {
        if (!self::$time) {
            self::$startTime = self::$time = YII_BEGIN_TIME;
        }
        $time = round(microtime(true) - self::$time, 2);
        HDev::trace(print_r($message, true) . " ($time s)\n");
        self::log($message, $level);
        self::$time = microtime(true);
    }

    public static function log($obj,$level=Logger::LEVEL_INFO)
    {
        Yii::getLogger()->log(print_r($obj, true), $level, 'debug.dump');
    }

    public static function logSaveError(Model $model, $throwException = true)
    {
        if (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        } else {
            $backtrace = debug_backtrace();
        }

        if (empty($_SERVER['argv'])) {
            $request = Yii::$app->request->getUrl();
        } else {
            $request = implode(' ', $_SERVER['argv']);
        }

        $class = get_class($model);
        self::log(array(
            "$class save error",
            'attrs' => $model->getAttributes(),
            'errors' => $model->getErrors(),
            'request' => $request,
            'file' => $backtrace[0]['file'],
            'line' => $backtrace[0]['line'],
        ), Logger::LEVEL_ERROR);

        if ($throwException) {
            throw new Exception("$class save error", [
                'attributes' => $model->getAttributes(),
                'errors' => $model->getErrors(),
            ]);
        }

    }

    public static function flash($data)
    {
        Yii::$app->getSession()->setFlash('info', "<pre>".VarDumper::dumpAsString($data, 10, true)."</pre>");
    }
}