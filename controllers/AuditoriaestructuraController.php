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

        $auditoria->load(['AuditoriaEstructura' => Yii::$app->request->post()]);
        $auditoria->Fecha = date('Y-m-d H:m:s');

        if (!$auditoria->save()) {
            $result['error'] = true;
            $result['mensaje'] = $auditoria->errors;
        }

        return $result;
    }

}
