<?php

namespace common\models;

use frontend\models\RegionUrlInterface;
use Yii;

/**
 * This is the model class for table "bank".
 *
 * @property integer $id
 * @property integer $_id
 * @property string $_date
 * @property string $name
 * @property string $name_genitive
 * @property string $name_prepositional
 * @property string $name_full
 * @property string $type
 * @property boolean $is_state_bank
 * @property boolean $is_foreign_bank
 * @property string $licence
 * @property string $site
 * @property integer $geoip_region_id
 * @property string $region
 * @property string $address
 * @property boolean $is_ssv
 * @property string $cb_info_url
 * @property string $personal_director
 * @property string $personal_director_date
 * @property string $code
 * @property string $banki_reference_date
 * @property boolean $is_top_bank
 *
 * relations
 * @property TopBank $top
 * @property Deposit[] $deposits
 * @property BankData $bankData
 * @property AgencyRating[] $agencyRatings
 * @property FolkRating $folkRating
 */
class ArchiveBank extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'archive_bank';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', '_id', '_date', 'name', 'type', 'is_foreign_bank', 'region', 'is_ssv', 'is_top_bank'], 'required'],
            [['is_state_bank', 'is_foreign_bank', 'is_ssv', 'is_top_bank'], 'boolean'],
            [['geoip_region_id'], 'integer'],
            [['personal_director_date', 'banki_reference_date'], 'safe'],
            [['name', 'name_genitive', 'name_prepositional', 'name_full', 'type', 'licence', 'site', 'region', 'address', 'cb_info_url', 'personal_director', 'code'], 'string', 'max' => 255]
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'name_genitive' => 'Родительный падеж',
            'name_prepositional' => 'Предложный падеж',
            'name_full' => 'Полное название',
            'type' => 'Тип',
            'is_state_bank' => 'С гос. участием',
            'is_foreign_bank' => 'с участием иностранного капитала',
            'licence' => 'Номер лицензии',
            'site' => 'Официальный сайт',
            'geoip_region_id' => 'Идентификатор города регистрации',
            'region' => 'Город регистрации',
            'address' => 'Адрес головного офиса',
            'is_ssv' => 'Участие в системе страхования вкладов',
            'cb_info_url' => 'Ссылка на информацию о банке на сайте ЦБ',
            'personal_director' => 'Руководитель исполнительного офиса',
            'personal_director_date' => 'Дата актуализации информации о руководителе исполнительного органа',
            'code' => 'Кодовый индентификатор банка',
            'banki_reference_date' => 'Дата составления справки на Banki.ru',
            'is_top_bank' => 'Входит в top-банков',
        ];
    }
}
