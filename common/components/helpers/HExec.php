<?php 

namespace common\components\helpers;

use Yii;

/**
 * Класс создан для запуска команд из консоли
 */
class HExec
{
    /**
     * Run Application in background
     *
     * @param  string $Command
     * @param  integer $Priority
     * @return integer PID
     */
    public static function background($Command, $Priority = 0){
        if($Priority)
            $PID = shell_exec("nohup nice -n $Priority $Command > /dev/null & echo $!");
        else
            $PID = shell_exec("nohup $Command > /dev/null & echo $!");
        return($PID);
    }

    /**
     * Check if the Application running !
     *
     * @param integer $PID
     * @return  boolean
     */
    public static function is_running($PID){
        exec("ps $PID", $ProcessState);
        return(count($ProcessState) >= 2);
    }

    /**
     * Kill Application PID
     *
     * @param  integer $PID
     * @return boolean
     */
    public static function kill($PID){
        if (self::is_running($PID)) {
            exec("kill -KILL $PID");
            return true;
        } else {
            return false;
        }
    }
}
?>