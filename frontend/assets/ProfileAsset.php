<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class profileAsset extends AssetBundle
{
    public $sourcePath = '@upload';

    public $js = [
        'js/dropzone.js',
        'js/profile.js',
    ];

    public $depends = [
        'common\assets\FontsAsset',
        'yii\web\JqueryAsset',
    ];
}
