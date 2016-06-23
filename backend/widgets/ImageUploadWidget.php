<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 11/25/15
 * Time: 3:38 PM
 */

namespace backend\widgets;

use backend\assets\FileUploadAsset;
use common\enums\ImageType;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class ActiveForm
 * @package backend\widgets
 *
 */
class ImageUploadWidget extends InputWidget
{
    public $type;

    public $src;

    public $removeButton = true;

    public function init()
    {
        if (!$this->type) {
            throw new InvalidConfigException("Image type not set");
        }
        if (!ImageType::hasValue($this->type)) {
            throw new InvalidConfigException("Unknown image type {$this->type}");
        }

        parent::init();
    }

    public function run()
    {
        $this->registerJs();

        $buttons[] = Html::tag('div', Html::tag('span', 'Загрузить').Html::fileInput('img').Html::activeHiddenInput($this->model, $this->attribute, ['class' => 'input-filename']),  ['class' => 'btn btn-primary fileinput-button', 'data-type' => $this->type]);

        if ($this->removeButton) {
            $buttons[] = Html::tag('div', Html::icon('remove'),  ['class' => 'btn btn-default fileinput-remove'.($this->src ? '' : ' disabled')]);
        }
        $input = Html::tag('div', Html::tag('div', implode("\n", $buttons), ['class' => 'btn-group btn-group-sm']), ['class' => 'upload-buttons']);
        if ($this->src) {
            $img = Html::img($this->src, ['onload' => "addSizes(this)"]);
        } else {
            $img = '';
        }
        $uploaded = Html::tag('div', $img, ['class' => 'uploaded-image']);
        return "$input\n$uploaded";
    }

    public function registerJs()
    {
        $this->view->registerJsFile("@web/js/upload.js", ['depends' => [FileUploadAsset::className()]]);
        $this->view->registerJs(<<<JS
function addSizes(img)
{
    var div = document.createElement('div');
    div.className = 'image-sizes';
    div.textContent = img.naturalWidth+'x'+img.naturalHeight+'px';
    img.parentNode.appendChild(div);
}
JS
            , View::POS_HEAD);
    }
}