<?php
namespace backend\models;

use Yii;

/**
 * ChangePasswordForm
 */
class ChangePasswordForm
{

    public $password;
    public $repeatPassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['password'], 'string', 'length' => [6, 25], 'message' => 'Пароль слишком простой, минимум 6 символов'],
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
        return parent::save(false, ['password']);
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!parent::validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный пароль');
            }
        }
    }
}
