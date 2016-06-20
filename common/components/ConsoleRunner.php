<?php

namespace common\components;

use common\components\helpers\HDev;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * ConsoleRunner - a component for running console commands on background.
 */
class ConsoleRunner extends Component
{
    /**
     * @var string Console application file that will be executed.
     * Usually it can be `yii` file.
     */
    public $file;

    public $outFile;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->file === null) {
            throw new InvalidConfigException('The "file" property must be set.');
        }
    }

    /**
     * Running console command on background
     *
     * @param string $cmd Argument that will be passed to console application
     * @return boolean
     */
    public function run($cmd, $outFile = "/dev/null", $priority = null)
    {
        $cmd = PHP_BINDIR . '/php ' . Yii::getAlias($this->file) . ' ' . $cmd;
        $outFile = Yii::getAlias($outFile);
        if ($priority)
            $pid = shell_exec("nohup nice -n $priority > $outFile & echo $!");
        else {
            HDev::log("nohup $cmd > $outFile & echo $!");
            $pid = shell_exec("nohup $cmd > $outFile & echo $!");
        }
        return $pid;
    }

    /**
     * Check if the Application running !
     *
     * @param integer $PID
     * @return  boolean
     */
    public function is_running($PID){
        exec("ps $PID", $ProcessState);
        return(count($ProcessState) >= 2);
    }

    /**
     * Kill Application PID
     *
     * @param  integer $PID
     * @return boolean
     */
    public function kill($PID){
        if (self::is_running($PID)) {
            exec("kill -KILL $PID");
            return true;
        } else {
            return false;
        }
    }

    private function getOutFile()
    {
        return $this->outFile ? Yii::getAlias($this->outFile) : "/dev/null";
    }
}
