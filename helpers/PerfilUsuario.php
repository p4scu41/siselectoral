<?php
namespace app\helpers;

use Yii;

class PerfilUsuario
{
    public static function isAdminGeneral()
    {
        if (isset(Yii::$app->user->identity)) {
            return (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idAdmin']));
        }

        return false;
    }

    public static function isAdminMunicipal()
    {
        if (isset(Yii::$app->user->identity)) {
            return (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idAdminMuni']));
        }

        return false;
    }

    public static function isCapturista()
    {
        if (isset(Yii::$app->user->identity)) {
            return (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idCapturista']));
        }

        return false;
    }

    public static function isDistritoLocal()
    {
        if (isset(Yii::$app->user->identity)) {
            return (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idDistrito']));
        }

        return false;
    }

    public static function isDistritoFederal()
    {
        if (isset(Yii::$app->user->identity)) {
            return (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idDistritoFederal']));
        }

        return false;
    }

    public static function hasPermiso($modulo, $permiso)
    {
        if (isset(Yii::$app->user->identity)) {
            $sql = 'SELECT * FROM [Permisos] WHERE
            [IdPerfilUsuario] = \''.Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil.'\' AND
            [IdModulo] = \''.$modulo.'\' AND
            [TipoPermiso] LIKE \'%'.$permiso.'%\'';

            $result = Yii::$app->db->createCommand($sql)->queryOne();

            if ($result != null) {
                return true;
            }
        }

        return false;
    }
}