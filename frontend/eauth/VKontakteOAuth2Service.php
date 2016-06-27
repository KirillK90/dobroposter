<?php
/**
 * An example of extending the provider class.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://github.com/Nodge/yii2-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace frontend\eauth;

use common\enums\Gender;
use common\helpers\HDates;

class VKontakteOAuth2Service extends \nodge\eauth\services\VKontakteOAuth2Service
{

	// protected $scope = 'friends';

	protected function fetchAttributes()
	{
		$tokenData = $this->getAccessTokenData();
		$info = $this->makeSignedRequest('users.get.json', array(
			'query' => array(
				'uids' => $tokenData['params']['user_id'],
				//'fields' => '', // uid, first_name and last_name is always available
				'fields' => 'nickname, sex, bdate, photo_medium, email',
			),
		));

		$info = $info['response'][0];

		$this->attributes = $info;
		$this->attributes['id'] = $info['uid'];
		$this->attributes['name'] = $info['first_name'] . ' ' . $info['last_name'];
		$this->attributes['url'] = 'http://vk.com/id' . $info['uid'];

		if (!empty($info['nickname'])) {
			$this->attributes['username'] = $info['nickname'];
		} else {
			$this->attributes['username'] = 'id' . $info['uid'];
		}

		$this->attributes['gender'] = $info['sex'] == 1 ? Gender::FEMALE : Gender::MALE;
		if (!empty($info['bdate'])) {
			$this->attributes['birthday'] = HDates::short($info['bdate'], 'd.m.Y');
		}

		if (!empty($info['photo_medium'])) {
			$this->attributes['photo_url'] = $info['photo_medium'];
		}

//        HDev::trace($info);
//        \Yii::$app->end();
		return true;
	}
}
