<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 11/18/15
 * Time: 9:57 AM
 */

namespace common\components;


use yii\log\Logger;

class EmailTarget extends \yii\log\EmailTarget
{
    /**
     * @var integer Ограничение на то как часто можно отправлять письма
     */
    public $minInterval = 5; // 5 секунд

    public function init()
    {
        parent::init();
        if ($this->enabled && $this->minInterval) {
            if (\Yii::$app->cache->get('email.sent')) {
                $this->enabled = false;
            } else {
                \Yii::$app->cache->set('email.sent', true, $this->minInterval);
            }
        }
    }

    /**
     * Sends log messages to specified email addresses.
     */
    public function export()
    {
        // moved initialization of subject here because of the following issue
        // https://github.com/yiisoft/yii2/issues/1446
        if (empty($this->message['subject'])) {
            $message = reset($this->messages);
            $this->message['subject'] = sprintf("%s %s: %s", \Yii::$app->id, Logger::getLevelName($message[1]), $message[2]);
        }
        $messages = array_map([$this, 'formatMessage'], $this->messages);
        $body = wordwrap(implode("\n", $messages), 70);
        $this->composeMessage($body)->send($this->mailer);
    }

}