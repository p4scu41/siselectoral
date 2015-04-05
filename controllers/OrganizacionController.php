<?php

namespace app\controllers;

use app\models\Organizaciones;
use yii\filters\AccessControl;

class OrganizacionController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['listintegrantesfromseccion'],
                'rules' => [
                    [
                        'actions' => ['listintegrantesfromseccion'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Obtiene la lista de integrantes de una determinada seccion
     *
     * @param type $idOrganizacion
     * @param type $idSeccion
     */
    public function actionListintegrantesfromseccion($idOrganizacion, $idSeccion)
    {
        $listIntegrantes = Organizaciones::getListIntegrantesFromSeccion($idOrganizacion, $idSeccion);

        return json_encode($listIntegrantes);
    }

}
