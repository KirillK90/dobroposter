<?php

/**
 * @author Paweł Bizley Brzozowski
 * @version 1.3
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace backend\widgets\AjaxDropdown\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for the AjaxDropDown javascript files.
 */
class AjaxDropdownAsset extends AssetBundle
{

    public $sourcePath = '@app/widgets/AjaxDropdown/js';
    public $depends    = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $minify = YII_DEBUG ? '' : '.min';
        $this->js[] = 'AjaxDropdown' . $minify . '.js';
        parent::init();
    }
}
