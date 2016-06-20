<?php
/**
 * An example of extending the provider class.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii2-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace frontend\eauth;

use yii\helpers\ArrayHelper;

class FacebookOAuth2Service extends \nodge\eauth\services\FacebookOAuth2Service
{

	protected $scopes = array(
		self::SCOPE_EMAIL,
		self::SCOPE_USER_BIRTHDAY,
		self::SCOPE_USER_HOMETOWN,
		self::SCOPE_USER_LOCATION,
		self::SCOPE_USER_PHOTOS,
	);

	/**
	 * http://developers.facebook.com/docs/reference/api/user/
	 *
	 * @see FacebookOAuth2Service::fetchAttributes()
	 */
	protected function fetchAttributes()
	{
		$info = $this->makeSignedRequest('me', ['query' => ['fields' => 'id,location,email,name,link,picture,cover']]);
		$this->attributes['id'] = $info['id'];
		$this->attributes['name'] = $info['name'];
		$this->attributes['photo_url'] = "https://graph.facebook.com/{$info['id']}/picture?type=large";
		$this->attributes['link'] = $info['link'];
		$this->attributes['email'] = ArrayHelper::getValue($info, 'email');

//        HDev::trace($info);
//        \Yii::$app->end();

		return true;

	}
}
