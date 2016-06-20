<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 26.06.15
 * Time: 16:16
 */

namespace frontend\assets;

use Yii;

class JuiAsset extends \yii\jui\JuiAsset
{
  public $css = [
  ];

  public $depends = [
      'yii\web\JqueryAsset',
  ];
}