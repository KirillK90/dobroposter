<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm-email', 'key' => $user->auth_key]);
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

Здравствуйте, <?= $user->username ?>!
Поздравляем с созданием новой учетной записи на сайте Vklad.ru. Вы получили это сообщение, так как ваш адрес <?=$user->email?> был использован при регистрации на сайте.


Для подтверждения регистрации перейдите по следующей ссылке:
<?= $confirmLink ?>

Вы сможете пользоваться своей учётной записью только после подтверждения регистрации.

Если вы не регистрировались на Vklad.ru, значит кто-то использовал для этого ваш email.
В этом случае просто удалите это письмо, а мы приносим вам извинения за неудобства.


С наилучшими пожеланиями,
Команда Вклад.ру



