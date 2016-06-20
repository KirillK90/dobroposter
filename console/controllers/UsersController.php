<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/20/15
 * Time: 5:36 PM
 */

namespace console\controllers;


use common\components\helpers\HDates;
use common\components\helpers\HStrings;
use common\enums\UserRole;
use common\models\User;
use console\components\UConsoleCommand;
use yii\db\Connection;
use yii\db\Query;

class UsersController  extends UConsoleCommand
{

    public function actionAdd($username = 'admin', $password = '123456', $role = UserRole::ADMIN)
    {
        if (User::findOne(['username' => $username])) {
            $this->log("Username $username already exists");
            return;
        }
        $user = new User();
        $user->username = $username;
        $user->email = "$username@polygant.ru";
        $user->role = $role;
        $user->setPassword($password);
        $user->generateAuthKey();
        if (!$user->save()) {
            $this->logSaveError($user);
        }
        $this->log($user->attributes);
        $this->endProfile();
    }

    public function actionLoad($flush = false)
    {
        if ($flush) {
            $cnt = User::deleteAll('forum_user_id is not null');
            $this->profile("$cnt users deleted");
        }
        /** @var Connection $db */
        $db = \Yii::$app->forumDb;
        $query = new Query();
        $query->select(['userId', 'username', 'email', 'password', 'salt', 'birthday', 'joindate'])->from('user')
            ->andWhere(['usergroupid' => [2,7,9,11,12]])
            ->orderBy('userId asc');

        $transaction = \Yii::$app->db->beginTransaction();
        $batchSize = 500;
        $cnt = 0;
        foreach($query->batch($batchSize, $db) as $users) {
            $cnt += count($users);
            $rows = [];
            foreach($users as $userRow) {
                $user = new User();
                $user->username = $userRow['username'];
                $user->email = $userRow['email'];
                if (strpos($user->email, '..') !== false) {
                    continue;
                }
                if (!$user->email) {
                    $user->email = HStrings::transliterate($user->username)."@banki.ru";
                }
                $user->password_hash = $userRow['password'];
                $user->password_salt = $userRow['salt'];
                $user->forum_user_id = $userRow['userId'];
                $user->created_at = HDates::long($userRow['joindate']);
                $user->updated_at = HDates::long();
                if ($userRow['birthday'] && strpos($userRow['birthday'], '00') === false) {
                    $user->birthday = HDates::short($userRow['birthday'], 'd-m-Y');
                }
                $user->generateAuthKey();
                if (!$user->save()) {
                    $this->logSaveError($user);
                }
                $attrs = $user->attributes;
                unset($attrs['id']);
                $rows[] = array_values($attrs);
            }
            $this->log("$cnt users saved");
        }
        $transaction->commit();

        $this->endProfile();
    }

}