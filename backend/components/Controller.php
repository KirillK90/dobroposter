<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/21/15
 * Time: 4:02 PM
 */

namespace backend\components;


use common\enums\UserRole;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

class Controller extends \common\components\Controller
{

    public $allowedRoles = [UserRole::ADMIN, UserRole::MODERATOR];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $user = $this->getUser();
        if ($user && !in_array($user->role, $this->allowedRoles)) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        return parent::beforeAction($action);
    }
}