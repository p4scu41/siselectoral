<?php

namespace app\controllers;

use Yii;
use app\models\CSeccion;

class SeccionController extends \yii\web\Controller
{
    public function actionGetbymunicipio()
    {
        $idMuni = Yii::$app->request->post('municipio');

        $secciones = CSeccion::find()
            ->select(['NumSector'])
            ->where(['IdMunicipio' => $idMuni])
            ->orderBy('NumSector ASC')
            ->asArray()
            ->all();
        
        return json_encode($secciones);
    }

}
