<?php
namespace app\helpers;

use Yii;

use yii\helpers\ArrayHelper;

class generarPassword
{
    public static $prefix = '$2y$10$';

    public static function getPassword($clave)
    {
        $salt = base64_encode(Yii::$app->params['salt']);
        $salt = str_replace('+', '.', $salt);
        $hash = crypt($clave, self::$prefix.$salt.'$');

        return $hash;
    }

    public static function checkPassword($clave, $hash)
    {
        $new_hash = self::getPassword($clave);

        return $hash == $new_hash;
    }
}