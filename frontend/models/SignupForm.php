<?php
namespace frontend\models;

use common\enums\PageType;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $accept = true;
    public $is_subscribed = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            [['username', 'email', 'password'], 'required', 'message' => 'Необходимо заполнить поле'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email', 'message' => 'Некорректный адрес почты'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Пользователь с такой почтой уже существует'],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Пароль не должен быть короче 6 символов'],

            ['accept', 'required', 'requiredValue' => true, 'message' => 'Необходимо подтвердить свое согласие.'],
            ['is_subscribed', 'boolean'],
        ];
    }

    /**
     * Returns the attribute labels.
     *
     * Attribute labels are mainly used for display purpose. For example, given an attribute
     * `firstName`, we can declare a label `First Name` which is more user-friendly and can
     * be displayed to end users.
     *
     * By default an attribute label is generated using [[generateAttributeLabel()]].
     * This method allows you to explicitly specify attribute labels.
     *
     * Note, in order to inherit labels defined in the parent class, a child class needs to
     * merge the parent labels with child labels using functions such as `array_merge()`.
     *
     * @return array attribute labels (name => label)
     * @see generateAttributeLabel()
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Имя',
            'email' => 'Почта',
            'password' => 'Пароль',
            'is_subscribed' => 'Подписаться на новости проекта',
            'accept' => 'Я подтверждаю свое согласие c <a class="link line-check__link" href="'.Url::to(['/site/page', 'pageType' => PageType::TERMS]).'">правилами сайта </a>',
        ];
    }


    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->is_subscribed = $this->is_subscribed;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->status = User::STATUS_NEW;
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Sends activation email
     * @return bool
     */
    public function sendEmail(){
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_NEW,
            'email' => $this->email,
        ]);

        return \Yii::$app->mailer->compose(['html' => 'confirmEmail-html', 'text' => 'confirmEmail-text'], ['user' => $user])
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Активация аккаунта ' . \Yii::$app->name)
            ->send();
    }

}
