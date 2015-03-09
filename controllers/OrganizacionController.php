<?php

namespace app\controllers;

use app\models\Organizaciones;

class OrganizacionController extends \yii\web\Controller
{
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
