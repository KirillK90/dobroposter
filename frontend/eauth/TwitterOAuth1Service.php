<?php
/**
 * An example of extending the provider class.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii2-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace frontend\eauth;

class TwitterOAuth1Service extends \nodge\eauth\services\TwitterOAuth1Service
{

	protected function fetchAttributes()
	{
		$info = $this->makeSignedRequest('account/verify_credentials.json');

		$this->attributes['id'] = $info['id'];
		$this->attributes['name'] = $info['name'];
		$this->attributes['url'] = 'http://twitter.com/account/redirect_by_id?id=' . $info['id_str'];

		$this->attributes['username'] = $info['screen_name'];
		$this->attributes['photo_url'] = $info['profile_image_url'];

//        HDev::trace($info);
//        \Yii::$app->end();

		return true;
	}

	/**
	 * Returns the error array.
	 *
	 * @param array $response
	 * @return array the error array with 2 keys: code and message. Should be null if no errors.
	 */
	protected function fetchResponseError($response)
	{
		if (isset($response['errors'])) {
			$first = reset($response['errors']);
			return array(
				'code' => $first['code'],
				'message' => $first['message'],
			);
		}
		return null;
	}
}