<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$prefixFunc = function($message) {
    if (empty($_SERVER['argv'])) {
        $request = Yii::$app->request->getUrl();
    } else {
        $request = implode(' ', $_SERVER['argv']);
    }
    return "[$request]";
};

return [
    'aliases' => [
        '@admin' => 'http://admin.vklad.ru',
        '@site' => 'http://vklad.ru',
        '@static' => 'http://static.vklad.ru',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'image' => array(
            'class' => 'yii\image\ImageDriver',
            'driver' => 'GD',  //GD or Imagick
        ),
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'email' => [
                    'class' => 'common\components\EmailTarget',
                    'levels' => ['error', 'warning'],
                    'message' => [
                        'from' => ['errors@dobroposter.ru'],
                        'to' =>  $params['devEmails'],
                    ],
                    'prefix' => $prefixFunc,
                    'enabled' => $params['sendDevEmails']
                ],
                'debug' => [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['debug.dump'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/dump.log',
                ],
            ]
        ]
    ],
    'modules' => [
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
            'uploadDir' => '@upload/redactor',
            'uploadUrl' => '@static/redactor',
//            'imageUploadRoute' => '/redactor/upload-image',
//            'fileUploadRoute' => '/redactor/upload-file'
        ],
    ],
];
