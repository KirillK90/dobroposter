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
class FilterAsset extends AssetBundle
{
    public $sourcePath = '@upload';

    public $css = [
        'css/jquery.formstyler.css',
    ];

    public $js = [
        'js/number-format.js',
        'js/str-to-num.js',
        'js/jquery.formstyler.js',
        'js/price-selector.js',
        'js/item-filter.js',
        'js/link-top.js'   ,
    ];

    public $depends = [
        'common\assets\FontsAsset',
        'frontend\assets\JuiAsset',
    ];
}
