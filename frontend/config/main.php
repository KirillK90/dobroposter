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
                '<action:(logout|signup|login|profile|search|eauth|confirm-email|reset-password|reset-password-request|subscribe|unsubscribe)>' => 'site/<action>',
                'poisk' => 'site/search',
                '<pageType:(about|contacts|adv|terms)>' => 'site/page',
                'registration' => 'site/signup',

                'cache/flush' => 'cache/flush',
                'ajax/regions/filter' => 'regions/filter',
                'ajax/regions/region-cities' => 'regions/region-cities',

                'vidy-vkladov/<categorySlug:[-\w\d]+>/spisok-gorodov' => 'regions/index',
                'vidy-vkladov/<slug:[-\w\d]+>/<regionSlug:[-\w\d]*>' => 'catalogs/category',
                'vidy-vkladov/<slug:[-\w\d]+>' => 'catalogs/category',
                'vidy-vkladov' => 'catalogs/index',

                'banki/vklady-<bankSlug:[-\w\d]+>/vklad-<depositSlug:[-\w\d]+>/spisok-gorodov' => 'regions/index',
                'banki/vklady-<bankSlug:[-\w\d]+>/vklad-<slug:[-\w\d]+>/similar' => 'deposits/similar',
                'banki/vklady-<bankSlug:[-\w\d]+>/vklad-<slug:[-\w\d]+>/<regionSlug:[-\w\d]*>' => 'deposits/view',
                'banki/vklady-<bankSlug:[-\w\d]+>/vklad-<slug:[-\w\d]+>' => 'deposits/view',
                'banki/vklady-<bankSlug:[-\w\d]+>/spisok-gorodov' => 'regions/index',
                'banki/vklady-<bankSlug:[-\w\d]+>/kategorii' => 'catalogs/bank',
                'banki/vklady-<slug:[-\w\d]+>/kategorii/<categorySlug:[-\w\d]+>' => 'banks/category',
                'banki/vklady-<slug:[-\w\d]+>/<regionSlug:[-\w\d]*>' => 'banks/view',
                'banki/vklady-<slug:[-\w\d]+>/' => 'banks/view',

                'poisk-vkladov/<regionSlug:[-\w\d]*>' => 'deposits/search',
                'poisk-vkladov' => 'deposits/search',
                'banki' => 'banks/index',

                'spisok-gorodov/<regionSlug:[-\w\d]+>' => 'regions/switch',
                'spisok-gorodov' => 'regions/index',

                'analitika/best' => 'articles/best',
                'analitika/chart' => 'articles/chart',

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
