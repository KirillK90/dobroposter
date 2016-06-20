<?php

namespace common\models;

use common\components\helpers\HStrings;
use common\components\helpers\SubstitutionsHelper;
use common\enums\EntityType;
use frontend\models\RegionUrlInterface;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "bank".
 *
 * @property integer $id
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
 * @property string $photo_head_office_preview
 * @property string $photo_head_office_big
 * @property boolean $is_ssv
 * @property string $cb_info_url
 * @property string $personal_director
 * @property string $personal_director_date
 * @property string $icon_url
 * @property string $logo_url
 * @property string $logo2_url
 * @property string $code
 * @property string $banki_reference_date
 * @property string $banki_reference_announce
 * @property string $banki_reference_part1
 * @property string $banki_reference_part2
 * @property string $bank_phones_callcenter
 * @property boolean $is_top_bank
 * @property boolean $has_deposits
 *
 * relations
 * @property TopBank $top
 * @property Deposit[] $deposits
 * @property BankData $bankData
 * @property AgencyRating[] $agencyRatings
 * @property FolkRating $folkRating
 */
class Bank extends \yii\db\ActiveRecord implements RegionUrlInterface, SubstitutionsInterface
{
    const DEFAULT_LOGO = '@static/images/empty_bank_logo.png';
    const DEFAULT_LOGO_SMALL = '@static/images/empty_bank_logo_small.png';

    public $objectsCount;

    /**
     * В случае если ссылка осталась, но Банк более не приходит по Апи
     * @var bool
     */
    public $deleted = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bank';
    }

    public static function getNotTopBanks($limit = null)
    {
        $query = self::find()
            ->joinWith('top')
            ->andWhere(['top_bank.id' => null]);
        if ($limit) {
            $query->limit($limit);
        }

        return $query->orderBy('bank.name')->all();
    }

    public static function getUrlList()
    {
        $list = [];
        $banks = Bank::find()->select(['name', 'id'])->indexBy('id')->orderBy('name')->column();
        foreach ($banks as $id => $name) {
            $list[] = ['name' => $name, 'url' => ["/banks/update", 'id' => $id]];
        }
        return $list;
    }

    /**
     * @return array
     */
    public static function getHandList()
    {
        $list = [];
        /** @var Bank[] $banks */
        $banks = Bank::find()
            ->select(['id', 'name', 'name_prepositional'])
            ->orderBy('name')
            ->all();

        foreach ($banks as $bank) {
            $list[mb_substr($bank->name, 0, 1)][] = $bank;
        }
        return $list;
    }

    /**
     * @return Bank[]
     */
    public static function getTopBanks()
    {
        return Bank::find()->innerJoinWith('top')->orderBy('top_bank.id')->all();
    }

    public static function getTopBanksListForProfFilter($limit = 100)
    {
        return Bank::find()->select(['name', 'bank.id'])
            ->innerJoinWith('rating')
            ->where('bank_rating.id < :limit', ['limit' => $limit])
            ->orderBy('bank.name')
            ->indexBy('id')
            ->column();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'type', 'is_foreign_bank', 'region', 'is_ssv', 'is_top_bank'], 'required'],
            [['is_state_bank', 'is_foreign_bank', 'is_ssv', 'is_top_bank'], 'boolean'],
            [['geoip_region_id'], 'integer'],
            [['personal_director_date', 'banki_reference_date', 'banki_reference_announce', 'banki_reference_part1', 'banki_reference_part2', 'bank_phones_callcenter'], 'safe'],
            [['name', 'name_genitive', 'name_prepositional', 'name_full', 'type', 'licence', 'site', 'region', 'address', 'photo_head_office_preview', 'photo_head_office_big', 'cb_info_url', 'personal_director', 'icon_url', 'logo_url', 'logo2_url', 'code'], 'string', 'max' => 255]
        ];
    }

    public function beforeValidate()
    {
        if (is_array($this->bank_phones_callcenter)) {
            $this->bank_phones_callcenter = json_encode($this->bank_phones_callcenter);
        }
        return parent::beforeValidate();
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
            'photo_head_office_preview' => 'Фото офиса превью',
            'photo_head_office_big' => 'Фото офиса',
            'is_ssv' => 'Участие в системе страхования вкладов',
            'cb_info_url' => 'Ссылка на информацию о банке на сайте ЦБ',
            'personal_director' => 'Руководитель исполнительного офиса',
            'personal_director_date' => 'Дата актуализации информации о руководителе исполнительного органа',
            'icon_url' => 'Иконка банка',
            'logo_url' => 'Лого 1',
            'logo2_url' => 'Лого 2',
            'code' => 'Кодовый индентификатор банка',
            'banki_reference_date' => 'Дата составления справки на Banki.ru',
            'banki_reference_announce' => 'Справка Banki.ru (краткий анонс)',
            'banki_reference_part1' => 'Справка на Banki.ru (начало)',
            'banki_reference_part2' => 'Справка на Banki.ru (окончание)',
            'bank_phones_callcenter' => 'Основные телефоны банка',
            'is_top_bank' => 'Входит в top-банков',
        ];
    }

    public function getNameGenitive()
    {
        return $this->name_genitive ?: $this->name;
    }

    public function getTop()
    {
        return $this->hasOne(TopBank::className(), ['bank_id' => 'id']);
    }

    public function getBankData()
    {
        return $this->hasOne(BankData::className(), ['id' => 'id']);
    }

    public function getRating()
    {
        return $this->hasOne(BankRating::className(), ['bank_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAgencyRatings()
    {
        return $this->hasMany(AgencyRating::className(), ['bank_id' => 'id'])
            ->indexBy('iblock_id')->orderBy('active_from');
    }

    /**
     * @return ActiveQuery
     */
    public function getFolkRating()
    {
        return $this->hasOne(FolkRating::className(), ['bank_id' => 'id']);
    }

    public function getObjects()
    {
        return $this->hasMany(BankObject::className(), ['bank_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDeposits()
    {
        return $this->hasMany(Deposit::className(), ['bank_id' => 'id'])->orderBy('product_name');
    }

    public function getDataModel()
    {
        return $this->bankData ? $this->bankData : new BankData(['id' => $this->id]);
    }

    public function getSlug()
    {
        return HStrings::transliterate($this->getNameGenitive());
    }

    public function getAlias()
    {
        return $this->hasOne(Alias::className(), ['entity_id' => 'id'])->where(['alias.entity_type' => EntityType::BANK])->orderBy(["alias.created_at" => SORT_DESC]);
    }

    public function getGenitiveSlug()
    {
        return $this->getSlug();
    }

    public function getPhonesHtml()
    {
        $rows = [];
        if ($this->bank_phones_callcenter) {
            $phones = json_decode($this->bank_phones_callcenter, true);
            foreach($phones as $data) {
                $rows[] = Html::tag('span', $data['phone'])." - ".Html::tag('em', ArrayHelper::getValue($data, 'phone_description'));
            }
        }
        return implode('<br>', $rows);
    }

    public function getAdminUrl()
    {
        return Url::to(['/banks/update', 'id' => $this->id]);
    }

    public function getUrl($region = null)
    {
        return Url::to(['/banks/view', 'slug' => $this->getGenitiveSlug(), 'regionSlug' => $region]);
    }

    public function getSubstitutions()
    {
        return SubstitutionsHelper::getBankSubstitutions($this);
    }

    public function getLogoUrl($small = false)
    {
        if ($small) {
            return $this->logo_url ? str_replace('http:', '', $this->logo_url) : Yii::getAlias(self::DEFAULT_LOGO_SMALL);
        } else {
            return $this->logo2_url ? str_replace('http:', '', $this->logo2_url) : Yii::getAlias(self::DEFAULT_LOGO);
        }
    }

    public function getActiveAgencyRatings()
    {
        $ratings = [];
        foreach ($this->agencyRatings as $iblockId => $agencyRating) {
            $value = $agencyRating->national_scalerating_value;
            if ($value && $value != 'отозван' && $value != 'приостановлен') {
                $ratings[$iblockId] = $value;
            }
        }
        return $ratings;
    }
}
