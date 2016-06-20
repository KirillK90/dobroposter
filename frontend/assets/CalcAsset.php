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
class CalcAsset extends AssetBundle
{
    public $sourcePath = '@upload';

    public $js = [
        'js/number-format.js',
        'js/str-to-num.js',
        'js/calc-main-functions.js',
        'js/box-slider-filter.js',
        'js/line-slider-filter.js',
    ];

    public $depends = [
        'frontend\assets\JuiAsset',
    ];
}
