<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 5/15/15
 * Time: 7:23 PM
 */

namespace common\widgets\HandListWidget;


use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

class HandListWidget extends Widget
{
    public $listData;

    public $path = 'handlist';

    /*
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!is_array($this->listData)) {
            throw new InvalidConfigException("'model' and 'attribute' properties must be specified.");
        }
    }

    public function run()
    {
        $groupList = [];
        foreach ($this->listData as $item) {
            if (is_array($item)) {
                $name = $item['name'];
                $url = isset($item['url'])? $item['url']: '';
                $value = Html::a($name, $url);
            } else {
                $name = $value = $item;
            }
            $groupList[mb_substr($name, 0, 1)][] = $value;
        }
        ksort($groupList);
        return $this->render($this->path, ['groupList' => $groupList]);
    }
}