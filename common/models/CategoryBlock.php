<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "main_icon".
 *
 * @property integer $id
 * @property integer $order
 * @property integer $category_id
 * @property string $title
 * @property string $filename
 *
 * Virtual attributes for upload scenario:
 *
 * @property string $uploadUrl @read-only
 * @property string $uploadPath @read-only
 * @property integer $maxOrder @read-only
 * @property CatalogCategory $category @read-only
 *
 */
class CategoryBlock extends \yii\db\ActiveRecord
{
    const IMAGE_WIDTH = 64;
    const EMPTY_IMG = 'empty.png';

    /** @var  UploadedFile */
    public $uploadedImage;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_block';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'title', 'filename'], 'required'],
            [['order', 'category_id'], 'integer'],
            [['title', 'category_id'], 'unique'],
            [['uploadedImage'], 'image',
                'minWidth' => self::IMAGE_WIDTH,
                'maxWidth' => self::IMAGE_WIDTH,
                'minHeight' => self::IMAGE_WIDTH,
//                'maxHeight' => self::IMAGE_HEIGHT,
                'extensions' => ['png', 'jpeg', 'gif', 'jpg'],
            ],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'category_id' => 'Подборка',
            'title' => 'Заголовок',
            'filename' => 'Иконку',
        ];
    }

    public function beforeSave($insert)
    {
        if (!$this->order) {
            $this->order = $this->maxOrder + 1;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return string
     */
    public function getUploadUrl()
    {
        return Yii::getAlias('@static/images/category_blocks');
    }

    /**
     * @return string
     */
    public function getUploadPath()
    {
        return Yii::getAlias('@upload/images/category_blocks');
    }

    public function getSrc()
    {
        return $this->uploadUrl."/".($this->filename ? $this->filename : self::EMPTY_IMG);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaxOrder()
    {
        return $this->hasMany(self::className(), [])->select('max("order")')->scalar();
    }

    /**
     * @return string
     */
    public function generateFilename()
    {
        return $this->filename = time() . '_' . $this->uploadedImage->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CatalogCategory::className(), ['id' => 'category_id']);
    }


    /**
     * @return boolean
     */
    public function upload()
    {
        if ($this->validate(['uploadedImage'])) {
            $filename = $this->generateFilename();
            $this->uploadedImage->saveAs("{$this->getUploadPath()}/{$filename}");
            return $filename;
        } else {
            return false;
        }
    }
}
