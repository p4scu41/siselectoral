<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\AuditoriaEstructura;

class AuditoriaestructuraController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['getauditoria', 'setauditoria'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionGetauditoria()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'auditoria' => []
        ];

        $result['auditoria'] = AuditoriaEstructura::findOne(['IdNodoEstructuraMov' => Yii::$app->request->post('id')]);

        return $result;
    }

    public function actionSetauditoria()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'error' => false,
            'mensaje' => '',
        ];

        $auditoria = AuditoriaEstructura::findOne(['IdNodoEstructuraMov' => Yii::$app->request->post('IdNodoEstructuraMov')]);

        if (empty($auditoria)) {
            $auditoria = new AuditoriaEstructura();
        }

        $params = Yii::$app->request->post();
        $params['Puesto'] = $params['Puesto'] ? $params['Puesto'] : 0;
        $params['Persona'] = $params['Persona'] ? $params['Persona'] : 0;
        $params['Seccion'] = $params['Seccion'] ? $params['Seccion'] : 0;
        $params['Celular'] = $params['Celular'] ? $params['Celular'] : 0;

        $auditoria->load(['AuditoriaEstructura' => $params]);
        $auditoria->Fecha = date('Y-m-d H:m:s');

        if (!$auditoria->save()) {
            $result['error'] = true;
            $result['mensaje'] = $auditoria->errors;
        }

        return $result;
    }

}
