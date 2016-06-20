<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class FontsAsset extends AssetBundle
{
    public $basePath = '@upload';
    public $baseUrl = '@static';
    public $css = [
        'css/fonts.css',
        'fonts/icon-fonts/style.css',
    ];
}
