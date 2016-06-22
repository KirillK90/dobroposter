<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language' => "ru-RU",
    'name' => 'Добропостер',
    'components' => [
        'view' => [
            'class' => 'common\components\View',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
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
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'forceCopy' => YII_DEBUG,
        ],
        'formatter' => [
            'dateFormat' => 'php:Y-m-d',
            'datetimeFormat' => 'php:d.m.Y H:i',
            'timeZone' => "UTC"
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            // Disable index.php
            'showScriptName' => false,
            // Disable r= routes
            'enablePrettyUrl' => true,
            'rules' => array(
                '<controller:(seo)>/<action:(update-default)>/<page:\w+>' => '<controller>/<action>',
                '<controller:(seo)>/<action:(create|update|delete)>/<page:\w+>/<param_id:\d+>' => '<controller>/<action>',
                '<controller:(page-banners)>/<action:(update)>/<place:\w+>' => '<controller>/<action>',
                '<controller:(page)>/<action:(update)>/<type:\w+>' => '<controller>/<action>',
                '<controller:(articles)>/<action:(create)>/<type:\w+>' => '<controller>/<action>',
                '<controller:(articles)>/<type:(page|gid)>s' => '<controller>/index',
                '<controller:(articles)>/<type:(news|analytics)>' => '<controller>/index',

                '<controller:\w+>' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:[-\w]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',

            ),

        ],
        'urlManagerFrontend' => [
            'class' => 'yii\web\UrlManager',
            // Disable index.php
            'showScriptName' => false,
            // Disable r= routes
            'enablePrettyUrl' => true,
            'suffix' => '/',
            'rules' => [
                [
                    'pattern' => 'novosti/<slug:[-\w\d]+>',
                    'route' => 'articles/view',
                    'defaults' => ['type' => 'news'],
                ],
                [
                    'pattern' => 'novosti',
                    'route' => 'articles/index',
                    'defaults' => ['type' => 'news'],
                ],
                [
                    'pattern' => 'analitika/<slug:[-\w\d]+>',
                    'route' => 'articles/view',
                    'defaults' => ['type' => 'analytics'],
                ],
                [
                    'pattern' => 'analitika',
                    'route' => 'articles/index',
                    'defaults' => ['type' => 'analytics'],
                ],
                [
                    'pattern' => 'gid/<slug:[-\w\d]+>',
                    'route' => 'articles/view',
                    'defaults' => ['type' => 'guides'],
                ],
                [
                    'pattern' => 'gid',
                    'route' => 'articles/index',
                    'defaults' => ['type' => 'guides'],
                ],
                [
                    'pattern' => '<slug:[-\w\d]+>',
                    'route' => 'articles/view',
                    'defaults' => ['type' => 'page'],
                ],
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['api'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/api.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'consoleRunner' => [
            'class' => 'common\components\ConsoleRunner',
            'file' => '@app/../yii' // or an absolute path to console file
        ]
    ],
    'params' => $params,
];
