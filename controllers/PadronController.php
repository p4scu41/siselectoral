<?php

namespace app\controllers;

use app\models\PadronGlobal;

class PadronController extends \yii\web\Controller
{
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

}
