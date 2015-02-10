<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'es',
    //'defaultRoute' => 'site',
    //'catchAll' => ['site/positiontree'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'vNE91fIN-_VwKkE9CF9WhRAR4nT17q_G',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => ['//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js'],
                    'jsOptions' => ['position' => \yii\web\View::POS_HEAD],
                ],
                'yii\web\BootstrapAsset' => [
                    'sourcePath' => null,
                    'css' => ['//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css'],
                    //'js' => ['//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js'],
                    'jsOptions' => ['position' => \yii\web\View::POS_HEAD],
                ],
                'yii\jui\JuiAsset' => [
                    'sourcePath' => null,
                    //'css' => ['//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css'],
                    'js' => ['@web/js/plugins/jquery-ui.min.js'],
                    'jsOptions' => ['position' => \yii\web\View::POS_HEAD],
                ],
            ],
        ],
        'urlManager' => [
            //'class' => 'yii\web\UrlManager',
            'enableStrictParsing' => true,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                '<controller:\w+>/?' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'module/<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                'site/page/view/<view:\w+>' => 'site/page',
            ],
        ],
    ],
    'modules' => [
        'datecontrol' =>  [
            'class' => 'kartik\datecontrol\Module',
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
