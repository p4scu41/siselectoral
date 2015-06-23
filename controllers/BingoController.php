<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\helpers\MunicipiosUsuario;

class BingoController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $municipios = MunicipiosUsuario::getMunicipios();
        
        return $this->render('index', [
            'municipios' => $municipios
        ]);
    }

    public function actionGetpromovidos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $promovidos = \app\models\Promocion::getByPromotor(Yii::$app->request->post('promotor'));

        return $promovidos;
    }

    public function actionSetparticipacion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $promovidos = array_filter(explode('|', Yii::$app->request->post('promovidos')));

        \app\models\Promocion::setParticipacion($promovidos);

        return true;
    }
}
