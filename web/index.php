<?php

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//ini_set('error_reporting', 22519); // E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE
ini_set('error_reporting', E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
ini_set('date.timezone', 'America/Mexico_City');

// Establece la sesion a 30 minutos
session_cache_expire(30);

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
