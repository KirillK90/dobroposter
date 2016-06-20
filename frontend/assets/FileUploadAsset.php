<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * FileUploadAsset
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
class FileUploadAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $css = [
        'blueimp-file-upload/css/jquery.fileupload.css'
    ];
    public $js = [
        'blueimp-file-upload/js/vendor/jquery.ui.widget.js',
        'blueimp-file-upload/js/jquery.iframe-transport.js',
        'blueimp-file-upload/js/jquery.fileupload.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
