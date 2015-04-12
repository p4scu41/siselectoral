<?php
namespace app\helpers;

use Yii;
use app\models\CMunicipio;
use yii\helpers\ArrayHelper;

class MunicipiosUsuario
{
    public static function getMunicipios()
    {
        $findMuni = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio');

        if (strtolower(Yii::$app->user->identity->perfil->IdPerfil) == strtolower(Yii::$app->params['idAdmin'])) {
            $listMunicipios = $findMuni->all();
        } elseif (strtolower(Yii::$app->user->identity->perfil->IdPerfil) == strtolower(Yii::$app->params['idDistrito'])) {
            $listMunicipios = $findMuni
                ->where(['DistritoLocal'=>Yii::$app->user->identity->persona->DISTRITOLOCAL])
                ->all();
        } else {
            $listMunicipios = $findMuni
                ->where(['IdMunicipio'=>Yii::$app->user->identity->persona->MUNICIPIO])
                ->all();
        }

        $municipios = ArrayHelper::map($listMunicipios, 'IdMunicipio', 'DescMunicipio');

        return $municipios;
    }
}