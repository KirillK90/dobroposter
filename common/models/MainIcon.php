<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "main_icon".
 *
 * @property integer $id
 * @property integer $order
 * @property string $url
 * @property string $title
 * @property string $filename
 * @property string $filename_hover
 *
 * Virtual attributes for upload scenario:
 * @property string $uploadedImage @read-only
 * @property string $uploadedHover @read-only
 *
 * @property string $uploadUrl @read-only
 * @property string $uploadPath @read-only
 *
 */
class MainIcon extends ActiveRecord
{
    const SCENARIO_UPLOAD = 'upload';

    const IMAGE_WIDTH = 122;
    const IMAGE_HEIGHT = 122;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'main_icon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order', 'url', 'title', 'filename', 'filename_hover'], 'required'],
            [['order'], 'integer'],
            [['uploadedImage', 'uploadedHover'], 'image',
                'minWidth' => self::IMAGE_WIDTH,
                'maxWidth' => self::IMAGE_WIDTH,
                'minHeight' => self::IMAGE_HEIGHT,
                'maxHeight' => self::IMAGE_HEIGHT,
                'extensions' => ['png', 'jpeg', 'gif', 'jpg'],
                'on' => self::SCENARIO_UPLOAD],
            [['url', 'title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function safeAttributes()
    {
        $attributes = parent::safeAttributes();
        $attributes = array_combine($attributes, $attributes);
        unset($attributes['uploadedImage']);
        unset($attributes['uploadedHover']);
        return array_keys($attributes);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'url' => 'Ссылка',
            'title' => 'Текст',
        ];
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        $upload = true;
        if ($this->getScenario() === self::SCENARIO_UPLOAD) {
            $upload = $this->getUploadedImage() || $this->getUploadedHover();
        }
        return $upload && parent::load($data, $formName);
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedImage()
    {
        static $temp = false;
        if ($temp === false) {
            $temp = UploadedFile::getInstance($this, 'uploadedImage');
        }
        return $temp;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedHover()
    {
        static $temp = false;
        if ($temp === false) {
            $temp = UploadedFile::getInstance($this, 'uploadedHover');
        }
        return $temp;
    }

    /**
     * @return string
     */
    public function getUploadUrl()
    {
        return Yii::getAlias('@static/images/main_icons');
    }

    /**
     * @return string
     */
    public function getUploadPath()
    {
        return Yii::getAlias('@upload/images/main_icons');
    }

    public function getSrc($hover=false)
    {
        return $this->uploadUrl."/". ($hover ? $this->filename_hover : $this->filename);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function generateFilename(UploadedFile $file)
    {
        return $this->filename = time() . '_' . $file->name;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function generateFilenameHover(UploadedFile $file)
    {
        return $this->filename_hover = time() . '_' . $file->name;
    }

    /**
     * @return boolean
     */
    public function upload()
    {
        if ($this->scenario !== self::SCENARIO_UPLOAD || !$this->validate()) {
            return false;
        }
        $upload = true;
        if ($image = $this->getUploadedImage()) {
            $upload = $image->saveAs("{$this->getUploadPath()}/{$this->generateFilename($image)}");
        }
        if (($hover = $this->getUploadedHover()) && $upload) {
            $upload = $hover->saveAs("{$this->getUploadPath()}/{$this->generateFilenameHover($hover)}");
        }
        return $upload;
    }
}
