<?php

namespace app\controllers;

use Yii;
use app\models\PadronGlobal;
use app\models\DetalleEstructuraMovilizacion;
use app\models\Puestos;

class PadronController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return 'Inicio';
    }
    /**
     * Obtiene los datos de una persona
     * 
     * @param uniqueidentifier $id ID del padron
     * @return JSON Datos de la persona
     */
    public function actionGet($id)
    {
        try {
            $padron = new PadronGlobal();

            $persona = $padron->findOne(['CLAVEUNICA'=>$id])->attributes;
            
            return json_encode($persona);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * 
     */
    public function actionPersona($id)
    {
        $persona = json_decode($this->actionGet($id));
        $estructura = DetalleEstructuraMovilizacion::findOne(['IdPersonaPuesto' => $id]);
        $puesto = Puestos::findOne(['IdPuesto' => $estructura->IdPuesto]);
        
        $puestoPersona = $puesto->Descripcion.' - '.$estructura->Descripcion;
        
        return $this->render('persona', [
            'persona' => $persona,
            'puesto' => $puestoPersona,
        ]);
    }

}
