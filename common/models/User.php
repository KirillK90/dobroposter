<?php
namespace common\models;

use common\components\helpers\HDates;
use common\components\helpers\HDev;
use common\components\helpers\HStrings;
use common\enums\Gender;
use common\enums\OAuthName;
use common\enums\UserRole;
use nodge\eauth\ErrorException;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $photo_filename
 * @property string $password_hash
 * @property string $password_salt
 * @property string $password_reset_token
 * @property string $email
 * @property string $role
 * @property string $last_visit
 * @property integer $forum_user_id
 * @property string $gender
 * @property string $birthday
 * @property string $auth_key
 * @property boolean $oauth
 * @property boolean $is_subscribed
 * @property string $oauth_service
 * @property integer $oauth_id
 * @property string $photo_url
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @method void touch($param)
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_NEW = 1;//Email not verified
    const STATUS_ACTIVE = 10;

    const SCENARIO_ADMIN_INSERT = 'scenario_admin_insert';

    public $newPassword;

    /**
     * @var array EAuth attributes
     */
    public $profile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public static function getPrivilegedList()
    {
        return self::find()->where(['not', ['role' => null]])->indexBy('id')->select('username')->column();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [[
            'class' => TimestampBehavior::className(),
            'value' => new Expression('NOW()')
        ]];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key'], 'required'],
            [['email', 'password_hash'], 'required', 'when'=> function(User $model){
                return !$model->oauth;
            }],
            [['email'], 'default', 'value' => '', 'when'=> function(User $model){
                return !$model->oauth;
            }],
            [['oauth_id', 'oauth_service'], 'required', 'when'=> function(User $model){
                return $model->oauth;
            }],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['role'], 'in', 'range' => UserRole::getValues()],
            [['gender'], 'in', 'range' => Gender::getValues()],
            [['newPassword'], 'required', 'on' => self::SCENARIO_ADMIN_INSERT],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_NEW, self::STATUS_DELETED]],
            [['birthday'], 'date', 'format' => 'php:Y-m-d'],
            [['newPassword'], 'string', 'length' => [6, 25], 'message' => 'Пароль слишком простой, минимум 6 символов'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя',
            'password' => 'Пароль',
            'newPassword' => 'Новый пароль',
            'gender' => 'Пол',
            'birthday' => 'Дата рождения',
            'email' => 'Почта',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'role' => 'Роль',
            'last_visit' => 'Последний вход',
            'lastVisit' => 'Последний вход',
            'oauth' => 'OAuth',
            'oauth_service' => 'Соц. сеть',
            'oauth_id' => 'OAuth Id',
            'is_subscribed' => 'Рассылка',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param \nodge\eauth\ServiceBase $service
     * @return User
     * @throws ErrorException
     */
    public static function findByEAuth($service) {
        if (!$service->getIsAuthenticated()) {
            throw new ErrorException('EAuth user should be authenticated before creating identity.');
        }

        $user = self::findOne(['oauth_service' => $service->getServiceName(), 'oauth_id' => $service->getId()]);
        if ($user && $user->status != self::STATUS_ACTIVE) {
            throw new ErrorException('User status is not active');
        }
        if (!$user) {
            $user = new User();
            $user->username = $service->getAttribute('name');
            $user->oauth = true;
            $user->oauth_id = $service->getId();
            $user->oauth_service = $service->getServiceName();
            $user->birthday = $service->getAttribute('birthday');
            $user->gender = $service->getAttribute('gender');
            $user->photo_url = $service->getAttribute('photo_url');
            $user->password_hash = '';
            $user->password_salt = '';
            $user->email = $service->getAttribute('email');

            if (!$user->validate('email')) {
                $user->email = '';
            }

            $user->generateAuthKey();
            if (!$user->save()) {
                HDev::logSaveError($user);
            }
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findIdentityByForumId($forum_id)
    {
        return static::findOne(['forum_user_id' => $forum_id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $email
     * @return User|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by auth_key
     *
     * @param string $auth_key
     * @return static|null
     */
    public static function findNewByAuthKey($auth_key)
    {
        return static::findOne([
            'auth_key' => $auth_key,
            'status' => self::STATUS_NEW,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = $this->generatePasswordHash($password);
    }

    public function generatePasswordHash($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getAdminUrl()
    {
        return Url::to(['/users/update', 'id' => $this->id]);
    }

    public function getLastVisit()
    {
        return $this->last_visit ?: 'никогда';
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function generateFilename($file)
    {
        return $this->photo_filename = $this->id.'_'.time().'.'.strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    /**
     * @return string
     */
    public function getUploadPath()
    {
        return Yii::getAlias('@upload/images/profile');
    }

    /**
     * @return string
     */
    public function imageUrl()
    {
        if ($this->photo_filename) {
            return Yii::getAlias('@static/images/profile') . '/' . $this->photo_filename;
        } elseif ($this->photo_url) {
            return $this->photo_url;
        } else {
            return Yii::getAlias("@static/images/empty_avatar.png");
        }
    }

    public function getService()
    {
        return OAuthName::getName($this->oauth_service);
    }

    public function registerForumUser()
    {
        $key = sha1(Yii::$app->params['forum.cookie_salt']);
        $username = HStrings::transliterate($this->username);
        //Проверяем, что такого юзера больше нет
        $existing_user = \Yii::$app->forumDb->createCommand('SELECT `username` FROM `user` where `username` regexp :regexp order by `username` desc limit 1',
            [':regexp'=>'^'.$username.'[1-9]*$'])->queryAll();
        //Если есть, берем следующий ник
        if(count($existing_user)>0) {
            $existing_user = str_replace($username, '', $existing_user[0]['username']);
            $username = $username . (intval($existing_user) + 1);
        }
        $email = $this->email;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,Yii::$app->getUrlManager()->getHostInfo()."/forum/reg.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(['key'=>$key,'username'=>$username,'email'=>$email]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        Yii::info($server_output,'forum.answer');
        curl_close ($ch);

        $reg_info = json_decode($server_output,true);
        if(!$reg_info['success']){
            Yii::warning("Forum registration error: ".$reg_info['message'], 'forum.signup');
        }
        $this->forum_user_id = $reg_info['userid'];
        if (!$this->save(true, ['forum_user_id'])) {
            HDev::logSaveError($this);
        }
    }

    public function updateForumPassword()
    {
        $result = false;
        if($this->forum_user_id){
            /** @var \yii\db\Connection $connection */
            $connection = \Yii::$app->forumDb;
            try {
                $result = $connection->createCommand('update `user` set `password`=:password,`salt`=:salt,`passworddate`=:passworddate where userid=:userid',[':password'=>$this->password_hash,':salt'=>$this->password_salt,':passworddate'=>HDates::short(time()+60*60*24*365),':userid'=>$this->forum_user_id])->execute();
            }catch (\Exception $e){
                Yii::error($e,'forum.errors');
            }
        }
        return $result;
    }
}
