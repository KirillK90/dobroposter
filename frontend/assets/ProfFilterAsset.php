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
class ProfFilterAsset extends AssetBundle
{
    public $sourcePath = '@upload';

    public $css = [
        'css/jquery.mCustomScrollbar.css',
    ];

    public $js = [
        'js/lodash.min.js',
        'js/jquery.mCustomScrollbar.js',
        'js/scrollbar.js',
        'js/nav-tab.js',
        'js/tinysort.js',
        'js/item-prof-filter.js'
    ];

    public $depends = [
        'frontend\assets\FilterAsset',
    ];
}
