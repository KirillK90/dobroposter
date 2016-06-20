<?php

namespace common\models;

use common\enums\BannerType;
use common\enums\PagePlace;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\validators\ImageValidator;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%banner}}".
 *
 * @property integer $id
 * @property string $place
 * @property string $name
 * @property string $type
 * @property string|UploadedFile $filename
 * @property string $url
 * @property string $code
 * @property string $created
 * @property boolean $no_border
 *
 * @property string $placeLabel [[getPlaceLabel()]]
 * @property string $typeLabel [[getTypeLabel()]]
 * @property string $uploadPath [[getUploadPath()]]
 * @property string $filenameUrl [[getFilenameUrl()]]
 * @property string $createdAt [[getCreatedAt()]]
 * @property string $html [[getHtml()]]
 */
class Banner extends \yii\db\ActiveRecord
{
    const SCENARIO_UPLOAD = 'upload';

    /** @var  UploadedFile */
    public $uploadedImage;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'banner';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className(), 'updatedAtAttribute' => false, 'createdAtAttribute' => 'created', 'value' => new Expression('now()')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['place', 'type', 'name'], 'required'],
            ['name', 'trim'],
            ['name', 'unique'],
            ['type', function(){
                if ($this->type === BannerType::CODE && !$this->code) {
                    $this->addError('code', 'Код обязателен.');
                } elseif ($this->type === BannerType::IMG && !$this->filename) {
                    $this->addError('uploadedImage', 'Необходимо загрузить картинку');
                } elseif ($this->type === BannerType::IMG && !$this->url) {
                    $this->addError('url', 'Укажите ссылку');
                }
            }],
            ['place', 'in', 'range' => PagePlace::getValues()],
            ['type', 'in', 'range' => BannerType::getValues()],
            ['code', 'safe'],
            ['uploadedImage', 'required', 'on' => [self::SCENARIO_UPLOAD]],
            ['uploadedImage', 'image', 'extensions' => ['png', 'jpg', 'jpeg', 'gif', 'svg']],
            ['uploadedImage', 'validateSize'],
            ['filename', 'validateImage', 'when' => function (Banner $model) {
                    return $model->type == BannerType::IMG;
            }],
            ['url', 'url', 'when' => function (Banner $model) {
                return $model->url != '#' && $model->type == BannerType::IMG;
            } ],
            ['no_border', 'boolean']
        ];
    }

    public function validateSize()
    {
        if ($this->place) {
            $validator = new ImageValidator(PagePlace::getSize($this->place));
            $validator->validateAttribute($this, 'uploadedImage');
        }
    }

    public function validateImage()
    {
        $sizes = getimagesize($this->getImagePath());
        if (!$sizes) {
            $this->addError('Файл не является картинкой');
        }
        list($width, $height) = $sizes;
        $size = PagePlace::getSize($this->place);
        if (($minWidth = ArrayHelper::getValue($size, 'minWidth')) && $width < $minWidth) {
            $this->addError('uploadedImage', "Файл «{$this->filename}» слишком маленький. Ширина должна быть более $minWidth пикселя.");
        }
        if (($maxWidth = ArrayHelper::getValue($size, 'maxWidth')) && $width > $maxWidth) {
            $this->addError('uploadedImage', "Файл «{$this->filename}» слишком большой. Ширина должна быть менее $maxWidth пикселя.");
        }
        if (($minHeight = ArrayHelper::getValue($size, 'minHeight')) && $height < $minHeight) {
            $this->addError('uploadedImage', "Файл «{$this->filename}» слишком маленький. Высота должна быть более $minHeight пикселя.");
        }
        if (($maxHeight = ArrayHelper::getValue($size, 'maxHeight')) && $height > $maxHeight) {
            $this->addError('uploadedImage', "Файл «{$this->filename}» слишком большой. Высота должна быть менее $maxHeight пикселя.");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'place' => 'Место',
            'type' => 'Тип',
            'name' => 'Наименование',
            'filename' => 'Файл',
            'uploadedImage' => 'Картинка',
            'no_border' => 'Скрыть рамку',
            'url' => 'Ссылка',
            'code' => 'Код',
            'created' => 'Создан',
        ];
    }

    /**
     * @return string
     */
    public function getPlaceLabel()
    {
        return PagePlace::getName($this->place);
    }

    /**
     * @return string
     */
    public function getTypeLabel()
    {
        return BannerType::getName($this->type);
    }

    /**
     * @return string
     */
    public function getUploadPath()
    {
        return Yii::getAlias('@upload/images/banners');
    }

    /**
     * @return string
     */
    public function getFilenameUrl()
    {
        return $this->filename ? Yii::getAlias('@static/images/banners') . '/' . $this->filename : null;
    }

    /**
     * @param null|string $format
     * @return string
     */
    public function getCreatedAt($format = null)
    {
        return Yii::$app->formatter->asDatetime($this->created, $format);
    }

    public function upload()
    {
        if ($this->scenario !== self::SCENARIO_UPLOAD || !$this->validate(['uploadedImage'])) {
            return false;
        }
        $upload = true;
        if ($image = $this->uploadedImage) {
            $upload = $image->saveAs("{$this->getUploadPath()}/{$this->generateFilename($image)}");
        }
        return $upload;

    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function generateFilename(UploadedFile $file)
    {
        return $this->filename = time() . '_' . $file->name;
    }

    public function getHtml()
    {
        return $this->type == BannerType::CODE ? $this->code : Html::a(Html::img($this->getFilenameUrl()), $this->url, ['target' => '_blank', 'rel' => 'nofollow']);
    }

    function __toString()
    {
        return $this->html;
    }

    private function getImagePath()
    {
        return $this->uploadPath."/".$this->filename;
    }

    public function getUpdateLink()
    {
        return Html::a($this->name, ['/banners/update', 'id' => $this->id]);
    }
}
