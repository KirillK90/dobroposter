<?php
namespace frontend\controllers;

use common\components\helpers\HDev;
use common\models\User;
use frontend\components\Controller;
use frontend\models\ChangePasswordForm;
use frontend\models\EventsFilter;
use frontend\models\LoginForm;
use frontend\models\PasswordResetChangeForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ProfileForm;
use frontend\models\SearchForm;
use frontend\models\SignupForm;
use frontend\models\SubscribeForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['profile', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new EventsFilter();
        return $this->render('front', ['model' => $model]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionPage()
    {
        return $this->render('page');
    }

    public function actionProfile()
    {
        /** @var ProfileForm $profileForm */
        $profileForm = ProfileForm::findOne($this->getUser()->id);

        if (Yii::$app->request->isAjax && $profileForm->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($errors = ActiveForm::validate($profileForm)) {
                return $errors;
            } else if ($profileForm->save(false)) {
                return ['success' => true];
            } else {
                HDev::logSaveError($profileForm);
                return ['success' => false];
            }
        }
        /** @var ChangePasswordForm $changePasswordForm */
        $changePasswordForm = ChangePasswordForm::findOne($this->getUser()->id);
        if (Yii::$app->request->isAjax && $changePasswordForm->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($changePasswordForm->save()) {
                $changePasswordForm->updateForumPassword();
                return ['success' => true];
            } else {
                return ActiveForm::validate($changePasswordForm);
            }
        }


        return $this->render('profile', compact('profileForm', 'changePasswordForm'));
    }

    public function actionSignup()
    {
        if ($this->getUser()) {
            $this->redirect(['/site/profile']);
        }

        $model = new SignupForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($errors = ActiveForm::validate($model)) {
                return $errors;
            }
            if ($userModel = $model->signup()) {
                if ($model->sendEmail()) {
                    return ['success' => true, 'message' => 'Регистрация прошла успешно. Следуйте инструкциям в письме, отправленом на указанный адрес.'];
                } else {
                    return ['success' => false, 'message' => 'Не удалось отправить email'];
                }
            } else {
                return ['success' => false, 'message' => 'cant signup'];
            }
        }
        return $this->render('signup', compact('model'));
    }

    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new LoginForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$model->validate()) {
                return ActiveForm::validate($model);
            }
            if ($model->login()) {
                if (!Yii::$app->user->isGuest) {
                    return $this->redirect(Yii::$app->request->referrer);
                }
                return Yii::$app->controller->goBack();
            } else {
                return ['success' => false, 'message' => 'cant signup'];
            }
        }
        return $this->goHome();
    }

    public function actionResetPasswordRequest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                return ['success' => true, 'message' => 'Запрос на восстановление пароля принят. Следуйте инструкциям в письме, отправленном на указанный адрес.'];
            } else {
                return ['success' => false, 'message' => 'Не удалось сбросить пароль для указанной почты.'];
            }
        }

        return ActiveForm::validate($model);
    }

    public function actionResetPassword($token)
    {
        /** @var PasswordResetChangeForm $model */
        $model = PasswordResetChangeForm::findByPasswordResetToken($token);

        if(!$model){
            throw new NotFoundHttpException("Ссылка для сброса пароля недействительна");
        }


        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$model->validate()) {
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                \Yii::$app->forumDb->createCommand('update `user` set `password`=:password, `salt`=:salt where userid=:userid',[':password'=>$model->password_hash,':salt'=>$model->password_salt,':userid'=>$model->forum_user_id])->execute();
                $this->setFlash('success', 'Пароль успешно изменен');
                return $this->redirect('/');
            } else {
                return ['success' => false, 'message' => 'Не удалось изменить пароль'];
            }
        }

        return $this->render('front', ['passwordForm'=>$model]);
    }

    public function actionConfirmEmail($key)
    {
        /** @var User $model */
        $model = User::findNewByAuthKey($key);
        if(!$model){
            throw new NotFoundHttpException("Ссылка для активации email недействительна");
        }
        $model->status = User::STATUS_ACTIVE;

        if($model->save()){
            if ($model->is_subscribed) {
                $subscribeForm = new SubscribeForm(['email' => $model->email]);
                if (!$subscribeForm->subscribe(false)) {
                    $model->updateAttributes(['is_subscribed' => false]);
                }
            }
            try {
                $model->registerForumUser();
            } catch(\Exception $e) {
                Yii::error("$e", 'forum.signup');
            }

        } else {
            throw new NotFoundHttpException("Ссылка для активации email недействительна");
        }

        if (Yii::$app->getUser()->login($model)) {
            $model->updateForumPassword();
            $this->setFlash('success','Email успешно подтвержден');
            return Yii::$app->controller->goHome();
        } else {
            throw new NotFoundHttpException("Ссылка для активации email недействительна");
        }
    }

    public function actionEauth()
    {
        $serviceName = Yii::$app->getRequest()->getQueryParam('service');
        if (isset($serviceName)) {
            /** @var $eauth \nodge\eauth\ServiceBase */
            $eauth = Yii::$app->get('eauth')->getIdentity($serviceName);
            $eauth->setRedirectUrl(Yii::$app->getUser()->getReturnUrl());
            $eauth->setCancelUrl(Yii::$app->getUrlManager()->createAbsoluteUrl('site/login'));

            try {
                if ($eauth->authenticate()) {

                    $identity = User::findByEAuth($eauth);

                    if ($identity->hasErrors()) {
                        throw new \yii\base\Exception('Не удалось осуществить вход');
                    }

                    Yii::$app->getUser()->login($identity);

                    // special redirect with closing popup window
                    $eauth->redirect();
                }
                else {
                    // close popup window and redirect to cancelUrl
                    $eauth->cancel();
                }
            }
            catch (\nodge\eauth\ErrorException $e) {
                // save error to show it later
                Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());

                // close popup window and redirect to cancelUrl
//              $eauth->cancel();
                $eauth->redirect($eauth->getCancelUrl());
            }
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionSearch($q = null)
    {
        $model = new SearchForm();
        $model->q = $q;
        return $this->render('search', ['model' => $model]);
    }

    public function actionUploadPhoto($user_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var ProfileForm $profile */
        $profile = ProfileForm::findOne($user_id);
        if (!$profile) {
            return ['result' => false, 'message' => 'User not found'];
        }
        $profile->setScenario($profile::SCENARIO_UPLOAD);
        $profile->load(Yii::$app->request->post());
        $profile->uploadedImage = UploadedFile::getInstance($profile, 'uploadedImage');

        if ($profile->upload()) {
            return ['result' => true, 'src' => $profile->imageUrl(), 'filename' => $profile->photo_filename];
        }
        return ['result' => false, 'message' => reset($profile->getErrors())];
    }

    public function actionSubscribe()
    {
        $model = new SubscribeForm();

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model->load(Yii::$app->request->post());

        if ($errors = ActiveForm::validate($model)) {
            return $errors;
        }
        /** @var User $user */
        $user = User::findByEmail($model->email);
        $needCheckEmail = !$user || $user->oauth;
        if ($model->subscribe($needCheckEmail)) {
            Yii::$app->response->cookies->add($model->getCookie());
            if ($user) {
                $user->updateAttributes(['is_subscribed' => true]);
            }
        } else {
            return [Html::getInputId($model, 'email') => ['При подписке произошла ошибка, попробуйте другую почту']];
        }

        return ['success' => true];
    }

    public function actionUnsubscribe($email)
    {
        $model = new SubscribeForm(['email' => $email]);
        if ($model->validate() && $model->unsubscribe()) {
            if ($user = User::findByEmail($model->email)) {
                $user->updateAttributes(['is_subscribed' => false]);
            }
            $this->setFlash('success', 'Вы успешно отписались от рассылки');
        } else {
            $this->setFlash('error', 'При отписке произошла ошибка, проверьте адрес ссылки');
        }

        return Yii::$app->controller->goHome();
    }
}
