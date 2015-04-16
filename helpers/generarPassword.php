<?php
namespace app\helpers;

use Yii;

use yii\helpers\ArrayHelper;

class generarPassword
{
    public static $cost = 11;

    public static function getPassword($clave)
    {
        $opciones = [
            'cost' => 11,
            'salt' => Yii::$app->params['salt'],
        ];

        return password_hash($clave, PASSWORD_BCRYPT, ['cost' => self::$cost, 'salt' => Yii::$app->params['salt']]);
    }

    public static function checkPassword($clave, $hash)
    {
        $new_hash = password_hash($clave, PASSWORD_BCRYPT, ['cost' => self::$cost, 'salt' => Yii::$app->params['salt']]);

        return $hash == $new_hash;
    }
}