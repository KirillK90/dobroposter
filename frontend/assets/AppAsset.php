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
class AppAsset extends AssetBundle
{
    public $sourcePath = '@upload';

    public $css = [
        'css/jquery.fancybox.css',
        'css/style.css',
        'dist/style.css',
    ];

    public $js = [
        'js/jquery.fancybox.js',
        'js/main.js',
        'js/link-top.js',
        'js/jquery.pin.js',
        'js/backToTop.js',
    ];

    public $depends = [
        'common\assets\FontsAsset',
        'yii\web\JqueryAsset',
    ];
}
