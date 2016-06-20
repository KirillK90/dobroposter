<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\UserFilter;
use common\enums\UserRole;
use common\models\User;
use Yii;
use yii\base\NotSupportedException;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

/**
 *  Users controller
 */
class UsersController extends Controller
{
    public $allowedRoles = [UserRole::ADMIN];

    public function actionIndex()
    {
        $filter = new UserFilter();
        $filter->load(Yii::$app->request->get());
        return $this->render('index', compact('filter'));
    }

    public function actionCreate()
    {
        $model = new User();
        $model->setScenario(User::SCENARIO_ADMIN_INSERT);
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            $model->setPassword($model->newPassword);
            $model->generateAuthKey();
            if ($model->save()) {
                $this->setFlash('success', ACTION_CREATE_SUCCESS);
                $this->redirect(['/users/update', 'id' => $model->id]);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findUser($id);

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->save()) {
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);

                if ($model->newPassword) {
                    $model->setPassword($model->newPassword);
                    if ($model->save()) {
                        if ($model->forum_user_id) {
                            if (!$model->updateForumPassword()) {
                                $this->setFlash('warning', 'User forum password wasn\'t changed.');
                            }
                        }
                        $this->setFlash('info', "Пароль успешно изменен");
                    }
                }

                $model->refresh();
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findUser($id);
        if ($model->delete()) {
            $this->setFlash('success', ACTION_DELETE_SUCCESS);
        } else {
            $this->setFlash('error', ACTION_VALIDATE_ERROR);
        }

        return $this->redirect(['/users']);
    }

    /**
     * @param $id
     * @return User
     * @throws NotSupportedException
     */
    protected function findUser($id)
    {
        if ($model = User::findOne($id)) {
            return $model;
        }
        throw new NotSupportedException('Пользователь не найден.');
    }

}
