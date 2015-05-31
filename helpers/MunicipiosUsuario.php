<?php
namespace app\helpers;

use Yii;
use app\models\CMunicipio;
use yii\helpers\ArrayHelper;

class MunicipiosUsuario
{
    public static function getMunicipios()
    {
        $municipios = [];

        if (isset(Yii::$app->user->identity)) {
            $findMuni = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio');

            if (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idAdmin'])) {
                $listMunicipios = $findMuni->all();
            } elseif (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idDistrito'])) {
                // Distrito LOCAL
                $idsMunicipio = Yii::$app->db->createCommand('SELECT DISTINCT [IdMunicipio] FROM [CSeccion] WHERE [DistritoLocal] = '.Yii::$app->user->identity->distrito)->queryAll();
                $listMunicipios = $findMuni
                    ->where('IdMunicipio IN ('.implode(',', ArrayHelper::map($idsMunicipio, 'IdMunicipio', 'IdMunicipio')).')')
                    ->all();
            } elseif (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idDistritoFederal'])) {
                // Distrito FEDERAL
                $idsMunicipio = Yii::$app->db->createCommand('SELECT DISTINCT [IdMunicipio] FROM [CSeccion] WHERE [DistritoFederal] = '.Yii::$app->user->identity->distrito)->queryAll();
                $listMunicipios = $findMuni
                    ->where('IdMunicipio IN ('.implode(',', ArrayHelper::map($idsMunicipio, 'IdMunicipio', 'IdMunicipio')).')')
                    ->all();
            } else {
                $listMunicipios = $findMuni
                    ->where(['IdMunicipio'=>Yii::$app->user->identity->persona->MUNICIPIO])
                    ->all();
            }

            $municipios = ArrayHelper::map($listMunicipios, 'IdMunicipio', 'DescMunicipio');
        }

        /*echo 'IdPerfil :'.strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil).'<br>';
        echo 'idDistrito :'.strtolower(Yii::$app->params['idDistrito']).'<br>';
        echo 'idDistritoFederal :'.strtolower(Yii::$app->params['idDistritoFederal']).'<br><br><br>';*/

        return $municipios;
    }
}