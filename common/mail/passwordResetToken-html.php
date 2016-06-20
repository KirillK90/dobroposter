<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Здравствуйте, <?= Html::encode($user->username) ?>!</p>

    <p>Мы получили запрос на  востановление пароля для вашей учетной записи на сайте Вклад.ру, которая была оформлена на данный электронный адрес. Если вы делали этот запрос, пожалуйста, следуйте инструкциям ниже.</p>
    <p>Пройдите по ссылке ниже, чтобы изменить свой пароль:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
    <p>Если вы не делали запрос на изменение пароля, просто проигнорируйте это сообщение. Будьте уверены, ваша учетная запись в безопасности. Ваш пароль останется прежним.</p>

    <hr />
    <p>С наилучшими пожеланиями,<br/>
        Команда Вклад.ру</p>
</div>

