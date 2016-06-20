<?php
return [
    'aliases' => [
        '@static' => 'http://static.vklad.local',
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=localhost;dbname=vklad',
            'username' => 'vklad_u',
            'password' => 'H5NMvs5l0A2b5',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
