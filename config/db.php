<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlsrv:Server=(local);Database=SIRECIDB',
    //'dsn' => 'dblib:host=192.168.1.243;dbname=SIRECIDB', // Para Linux con freetds
    'username' => 'admin',
    'password' => '123',
    'charset' => 'utf8',
];
