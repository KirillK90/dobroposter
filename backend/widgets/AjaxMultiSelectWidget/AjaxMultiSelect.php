<?php
/**
 * @copyright Copyright (c) 2015 Polygant
 * @link http://polygant.ru
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace backend\widgets\AjaxMultiSelectWidget;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * AjaxMultiselect renders a [Plygant ajax Multiselect widget]
 */
class AjaxMultiSelect extends InputWidget
{
	/**
	 * @var array data for generating the list options (value=>display)
	 */
	public $data = [];
	/**
	 * @var array the options for the Bootstrap Multiselect JS plugin.
	 * Please refer to the Bootstrap Multiselect plugin Web page for possible options.
	 */
	public $clientOptions = [];

	/**
	 * Initializes the widget.
	 */
	public function init()
	{
        //But now can. Why not?:)
		/*if (empty($this->data)) {
			throw new  InvalidConfigException('"Multiselect::$data" attribute cannot be blank or an empty array.');
		}*/
		parent::init();
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		if ($this->hasModel()) {
			echo Html::activeDropDownList($this->model, $this->attribute, $this->data, $this->options);
		} else {
			echo Html::dropDownList($this->name, $this->value, $this->data, $this->options);
		}
		$this->registerPlugin();
	}

	/**
	 * Registers MultiSelect Bootstrap plugin and the related events
	 */
	protected function registerPlugin()
	{
		$view = $this->getView();

		MultiSelectAsset::register($view);

		$id = $this->options['id'];

		$options = $this->clientOptions !== false && !empty($this->clientOptions)
			? Json::encode($this->clientOptions)
			: '';

		$js = "jQuery('#$id').multiselect($options);";
		$view->registerJs($js);
	}
}
