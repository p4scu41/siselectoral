<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\helpers\MunicipiosUsuario;
use app\models\Promocion;
use app\models\DetalleEstructuraMovilizacion;
use app\models\PadronGlobal;

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

    public function actionGetpromovidosseccion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $promovidos = Promocion::getByDepentNodo(Yii::$app->request->post('nodoseccion'));

        return $promovidos;
    }

    public function actionSetparticipacion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $promovidos = array_filter(explode('|', Yii::$app->request->post('promovidos')));

        Promocion::setParticipacion($promovidos);

        return true;
    }

    public function actionGetavance()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $muni = Yii::$app->getRequest()->post('municipio');
        $idNodo = '';
        
        if (!empty($muni)) {
            $coordMuni = DetalleEstructuraMovilizacion::getCoordMuni($muni);
            $idNodo = $coordMuni['IdNodoEstructuraMov'];
        } else {
            $idNodo = Yii::$app->getRequest()->post('idNodo');
        }

        if (empty($idNodo)){
            return null;
        }

        $avanceBingo = Promocion::getAvanceBingo($idNodo);
        $nodo = DetalleEstructuraMovilizacion::getInfoNodo($idNodo);
        $metaPromo = DetalleEstructuraMovilizacion::getMetaByPromotor($idNodo);

        $procentaje_avance = !empty($metaPromo['MetaByPromotor']) ? round($avanceBingo['total_participacion']/$metaPromo['MetaByPromotor']*100) : 0;

        return [
            'Meta' => $metaPromo,
            'Promovidos' => $avanceBingo['total_participacion'],
            'Avance' => $procentaje_avance,
            'TELMOVIL' => $nodo['TELMOVIL'],
            'TELCASA' => $nodo['TELCASA'],
        ];
    }

    public function actionGetinfopromotor()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $idNodo = Yii::$app->getRequest()->post('id');

        if (empty($idNodo)) {
            return null;
        }

        $promotor = DetalleEstructuraMovilizacion::getInfoNodo($idNodo);
        $promotor['foto'] = PadronGlobal::getFotoByUID($promotor['CLAVEUNICA'], $promotor['SEXO']);

        return $promotor;
    }
}
