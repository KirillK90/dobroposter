<?php
/**
 * YandexOAuth2Service class file.
 *
 * Register application: https://oauth.yandex.ru/client/my
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii2-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace frontend\eauth;

use common\enums\Gender;

/**
 * Yandex OAuth provider class.
 *
 * @package application.extensions.eauth.services
 */
class YandexOAuth2Service extends \nodge\eauth\services\YandexOAuth2Service
{
	public $name = 'yandex';

	protected function fetchAttributes()
	{
		$info = $this->makeSignedRequest('https://login.yandex.ru/info');

		$this->attributes['id'] = $info['id'];
		$this->attributes['name'] = $info['real_name'];
		//$this->attributes['login'] = $info['display_name'];
		//$this->attributes['email'] = $info['emails'][0];
		//$this->attributes['email'] = $info['default_email'];
		$this->attributes['gender'] = ($info['sex'] == 'male') ? Gender::MALE : Gender::FEMALE;

		return true;
	}

}