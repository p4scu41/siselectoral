<?php

namespace app\controllers;

use Yii;
use app\models\CSeccion;
use yii\filters\AccessControl;

class SeccionController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['getbymunicipio'],
                'rules' => [
                    [
                        'actions' => ['getbymunicipio'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

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