<?php

namespace backend\models;

use common\components\FilterModel;
use common\enums\BannerType;
use common\enums\PagePlace;
use common\models\Banner;
use Yii;

/**
 * Class BannersFilter
 * @package backend\models
 */
class BannersFilter extends FilterModel
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var string
     */
    public $place;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $filename;
    /**
     * @var string
     */
    public $url;
    /**
     * @var string
     */
    public $code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['place', 'in', 'range' => PagePlace::getValues()],
            ['type', 'in', 'range' => BannerType::getValues()],
            [['filename', 'url', 'code'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return (new Banner())->attributeLabels();
    }

    public function createQuery()
    {
        return Banner::find();
    }

    public function filters()
    {
        return ['and',
            [
                'id' => $this->id,
                'place' => $this->place,
                'type' => $this->type,
            ],
            ['like', 'filename', $this->filename],
            ['like', 'url', $this->url],
            ['like', 'code', $this->code],
        ];
    }
}