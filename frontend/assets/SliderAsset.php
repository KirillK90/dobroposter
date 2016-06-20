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
class SliderAsset extends AssetBundle
{
    public $sourcePath = '@upload';

    public $js = [
        'js/jquery.carouFredSel-6.2.1.js',
        'js/slider.js',
    ];

    public $depends = [
        'frontend\assets\JuiAsset',
    ];
}
