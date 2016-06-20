<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class FontsAsset extends AssetBundle
{
    public $basePath = '@upload/fonts';
    public $baseUrl = '@static/fonts';
    public $css = [
        'icon-fonts/style.css',
        'icon-fonts/ie7/ie7.css',
    ];
}
