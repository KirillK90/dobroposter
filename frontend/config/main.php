<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'language' => "ru-RU",
    'name' => 'Dobroposter.ru',
    'components' => [
        'user' => [
            'class' => 'frontend\components\User',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => '/',
            'identityCookie' => [
                'name' => '_identity',
                'domain' => $params['cookie.domain'],
            ],
        ],
        'session' => [
            'cookieParams' => [
                'domain' => $params['cookie.domain'],
            ],
        ],
        'request' => [
            'cookieValidationKey' => $params['cookie.validation_key'],
        ],
        'response' => [
            'class' => 'frontend\components\Response',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            // Disable index.php
            'showScriptName' => false,
            // Disable r= routes
            'enablePrettyUrl' => true,
            'suffix' => '/',
            'rules' => [
                '<controller:\w+>' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:[-\w]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logVars' => [],
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/forum.log',
                    'logVars' => [],
                    'categories' => ['forum.*']
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/eauth.log',
                    'logVars' => [],
                    'categories' => ['eauth.*']
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/mailchimp.log',
                    'logVars' => [],
                    'categories' => ['mailchimp.*']
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

		'view' => [
			'class' => 'frontend\components\View',
		],

        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => !YII_DEBUG,
            'forceCopy' => YII_DEBUG
        ],

        'eauth' => [
            'class' => 'nodge\eauth\EAuth',
            'popup' => true, // Use the popup window instead of redirecting.
            'cache' => false, // Cache component name or false to disable cache. Defaults to 'cache' on production environments.
            'cacheExpire' => 0, // Cache lifetime. Defaults to 0 - means unlimited.
            'httpClient' => [
                // uncomment this to use streams in safe_mode
                //'useStreamsFallback' => true,
            ],
            'services' => [ // You can change the providers and their classes.
            ],
        ],
        'i18n' => [
            'translations' => [
                'eauth' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@eauth/messages',
                ],
            ],
        ],
    ],
    'params' => $params,
];
