<?php
/**
 * Created by PhpStorm.
 * User: sobakasobaka
 * Date: 2015-09-14
 * Time: 18:26
 */

namespace frontend\components;


use Yii;
use yii\base\InvalidConfigException;

class Response extends \yii\web\Response
{
    /**
     * Sends the cookies to the client.
     */
    protected function sendCookies()
    {
        if ($this->getCookies() === null) {
            return;
        }
        $request = Yii::$app->getRequest();
        if ($request->enableCookieValidation) {
            if ($request->cookieValidationKey == '') {
                throw new InvalidConfigException(get_class($request) . '::cookieValidationKey must be configured with a secret key.');
            }
            $validationKey = $request->cookieValidationKey;
        }
        foreach ($this->getCookies() as $cookie) {
            $value = $cookie->value;
            if ($cookie->expire != 1  && isset($validationKey) && !($cookie instanceof PlainCookie)) {
                $value = Yii::$app->getSecurity()->hashData(serialize([$cookie->name, $value]), $validationKey);
            }
            setcookie($cookie->name, $value, $cookie->expire, $cookie->path, $cookie->domain, $cookie->secure, $cookie->httpOnly);
        }
    }
}