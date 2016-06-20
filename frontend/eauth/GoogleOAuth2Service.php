<?php

namespace frontend\eauth;
use common\enums\Gender;

/**
 * Yandex OAuth provider class.
 *
 * @package application.extensions.eauth.services
 */
class GoogleOAuth2Service extends \nodge\eauth\services\GoogleOAuth2Service
{
	public $name = 'google';

    protected $scopes = array(self::SCOPE_USERINFO_PROFILE, self::SCOPE_USERINFO_EMAIL);

    protected function fetchAttributes()
    {
        $info = $this->makeSignedRequest('https://www.googleapis.com/oauth2/v1/userinfo');

        $this->attributes['id'] = $info['id'];
        $this->attributes['name'] = $info['name'];

        if (!empty($info['link'])) {
            $this->attributes['url'] = $info['link'];
        }

        if (!empty($info['gender']))
            $this->attributes['gender'] = $info['gender'] == 'male' ? Gender::MALE : Gender::FEMALE;

        if (!empty($info['picture']))
            $this->attributes['photo_url'] = $info['picture'];

        if (!empty($info['email']))
            $this->attributes['email'] = $info['email'];

        if (!empty($info['birthday']))
            $this->attributes['birthday'] = $info['birthday'];

        \Yii::info('Fetch attributes:'.print_r($info, true), 'eauth.fetch.google');
    }

}