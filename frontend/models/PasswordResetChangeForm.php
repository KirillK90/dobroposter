<?php
namespace frontend\models;

use common\models\User;
use Yii;

/**
 * ChangePasswordForm
 */
class PasswordResetChangeForm extends User
{

    public $repeatPassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['repeatPassword', 'newPassword'], 'required'],
            [['newPassword'], 'string', 'length' => [6, 25], 'message' => 'Пароль слишком простой, минимум 6 символов'],
            ['repeatPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'newPassword' => 'Новый пароль',
            'repeatPassword' => 'Повторить пароль',
        ];
    }


    public function save($runValidation = true, $attributeNames = null)
    {
        if (!$this->validate()) {
            return false;
        }
        parent::setPassword($this->newPassword);
        return parent::save(false);
    }
}
