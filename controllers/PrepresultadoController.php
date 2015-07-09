<?php

namespace app\controllers;

use Yii;
use app\models\PREPTipoEleccion;
use app\models\PREPSeccion;
use app\models\PREPCandidato;
use app\models\PREPVoto;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\helpers\MunicipiosUsuario;

/**
 * PrepvotoController implements the CRUD actions for PREPVoto model.
 */
class PrepresultadoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $tiposEleccion = ArrayHelper::map(PREPTipoEleccion::find()->select('id_tipo_eleccion, descripcion')->all(), 'id_tipo_eleccion', 'descripcion');
        $municipios = MunicipiosUsuario::getMunicipios();
        $distritosLocales = ArrayHelper::map(
            PREPSeccion::find()->select('distrito_local')->groupBy('distrito_local')->orderBy('distrito_local')->all(),
            'distrito_local', 'distrito_local');
        $distritosFederales = ArrayHelper::map(
            PREPSeccion::find()->select('distrito_federal')->groupBy('distrito_federal')->orderBy('distrito_federal')->all(),
            'distrito_federal', 'distrito_federal');
        $candidatos = [];

        if (Yii::$app->request->post('tipoEleccion') != null) {
            if (Yii::$app->request->post('municipio') != null) {
                $where = 'municipio = '.(int)Yii::$app->request->post('municipio');
            } else if (Yii::$app->request->post('distritoLocal') != null) {
                $where = 'distrito_local = '.(int)Yii::$app->request->post('distritoLocal');
            } else if (Yii::$app->request->post('distritoFederal') != null) {
                $where = 'distrito_federal = '.(int)Yii::$app->request->post('distritoFederal');
            }

            $candidatos = ArrayHelper::map(PREPCandidato::find()->where($where)->andWhere('activo = 1')->all(), 'id_candidato', 'nombre');
        }

        return $this->render('index', [
            'tiposEleccion' => $tiposEleccion,
            'municipios' => $municipios,
            'distritosLocales' => $distritosLocales,
            'distritosFederales' => $distritosFederales,
            'candidatos' => $candidatos,
        ]);
    }

    /**
     * Finds the PREPVoto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id_partido
     * @param integer $id_casilla_seccion
     * @return PREPVoto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_partido, $id_casilla_seccion)
    {
        if (($model = PREPVoto::findOne(['id_partido' => $id_partido, 'id_casilla_seccion' => $id_casilla_seccion])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $nameColum = '';
        $valueColum = '';

        switch (Yii::$app->request->post('tipoEleccion')) {
            case '1': // Presidencia Municipal
                $nameColum = 'municipio';
                $valueColum = Yii::$app->request->post('municipio');
                break;
            case '2': // Diputación Local
                $nameColum = 'distrito_local';
                $valueColum = Yii::$app->request->post('distritoLocal');
                break;
            case '3': // Diputación Federal
                $nameColum = 'distrito_federal';
                $valueColum = Yii::$app->request->post('distritoFederal');
                break;
            default:
                return [];
        }

        $result = PREPVoto::getResultados(
            (int)Yii::$app->request->post('candidato'),
            $nameColum,
            (int)$valueColum,
            (int)Yii::$app->request->post('iniSeccion'),
            (int)Yii::$app->request->post('finSeccion'));

        return $result;
    }
}
