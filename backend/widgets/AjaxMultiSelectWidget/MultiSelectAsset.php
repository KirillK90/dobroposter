<?php
/**
 * @copyright Copyright (c) 2015 Polygant
 * @link http://polygant.ru
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace backend\widgets\AjaxMultiSelectWidget;

use yii\web\AssetBundle;

class MultiSelectAsset extends AssetBundle
{
	public $sourcePath = '@backend/widgets/AjaxMultiSelectWidget/assets';

	public $js = [
		'js/ajax-multiselect.js'
	];

	public $css = [
		'css/ajax-multiselect.css'
	];

	public $depends = [
		'yii\bootstrap\BootstrapPluginAsset'
	];
}