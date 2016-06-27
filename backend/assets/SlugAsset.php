<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * FileUploadAsset
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
class SlugAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $js = [
        'speakingurl/lib/speakingurl.js',
    ];
}
