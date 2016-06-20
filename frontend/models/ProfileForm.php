<?php
namespace frontend\models;

use common\enums\Gender;
use common\models\User;
use Yii;
use yii\image\drivers\Image;
use yii\web\UploadedFile;

/**
 * Login form
 */
class ProfileForm extends User
{
    const SCENARIO_UPLOAD = 'upload';

    public $birthday_d;
    public $birthday_m;
    public $birthday_y;

    /** @var  UploadedFile */
    public $uploadedImage;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['username', 'gender'], 'required', 'message' => 'Необходимо заполнить поле'],
            [['email'], 'required', 'when'=> function(User $model){
                return !$model->oauth;
            }],
            [['birthday_d', 'birthday_m', 'birthday_y'], 'safe'],
            [['birthday'], 'applyParts', 'skipOnEmpty' => false],
            [['birthday'], 'required'],
            [['birthday'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Заполните год'],
            // rememberMe must be a boolean value
            [['email'], 'unique','message'=>'Пользователь с такой почтой уже существует'],
            [['gender'], 'in', 'range' => Gender::getValues()],

            ['uploadedImage', 'required', 'on' => [self::SCENARIO_UPLOAD]],
            ['uploadedImage', 'image', 'extensions' => ['png', 'jpg', 'jpeg', 'gif', 'svg']],
            ['is_subscribed', 'boolean'],
            ['is_subscribed', 'validateSubscription']
        ];
    }

    public function validateSubscription()
    {
        if ($this->is_subscribed && !$this->email) {
            $this->addError('is_subscribed', "Чтобы подписаться на рассылку укажите Почту");
        }

        if ($this->isAttributeChanged('is_subscribed') || $this->isAttributeChanged('email')) {
            $subscribeForm = new SubscribeForm(['email' => $this->email]);
            if ($this->is_subscribed) {
                if (!$subscribeForm->subscribe($this->oauth)) {
                    $this->addError('is_subscribed', 'Не удалось подписатьcя на рассылку');
                }
            } elseif (!$this->isAttributeChanged('email')) {
                if (!$subscribeForm->unsubscribe()) {
                    $this->addError('is_subscribed', 'Не удалось отписаться от рассылки');
                }
            }
        }
    }

    public function applyParts()
    {
        $this->birthday = sprintf('%s-%s-%s', $this->birthday_y, $this->birthday_m, $this->birthday_d);
    }

    public function afterFind()
    {
        if ($this->birthday) {
            list($this->birthday_y, $this->birthday_m, $this->birthday_d) = explode('-', $this->birthday);
        }
        parent::afterFind();
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя',
            'password' => 'Пароль',
            'gender' => 'Пол',
            'birthday' => 'Дата рождения',
            'email' => 'Почта',
            'service' => 'Сервис',
            'is_subscribed' => 'Подписаться на новости проекта',
        ];
    }

    public function upload()
    {
        if ($this->scenario !== self::SCENARIO_UPLOAD || !$this->validate(['uploadedImage'])) {
            return false;
        }
        $upload = true;
        if ($file = $this->uploadedImage) {
            /** @var Image $image */
            $image=Yii::$app->image->load($file->tempName);
            $image->resize(100, 100, Image::ADAPT);

            $upload = $image->save("{$this->getUploadPath()}/{$this->generateFilename($file)}");
        }
        return $upload && $this->save(false, ['photo_filename']);

    }
}
