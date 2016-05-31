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
use app\helpers\PerfilUsuario;

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
                'only' => ['index', 'view', 'create', 'update', 'delete', 'get'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'get'],
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
        if (!PerfilUsuario::hasPermiso('19cff826-d301-48bd-a824-960c67b7d6f6', 'R')) {
            return $this->redirect(['site/index']);
        }

        $tiposEleccion = ArrayHelper::map(PREPTipoEleccion::find()->select('id_tipo_eleccion, descripcion')->all(), 'id_tipo_eleccion', 'descripcion');
        $municipios = MunicipiosUsuario::getMunicipios();
        $distritosLocales = ArrayHelper::map(
            PREPSeccion::find()->select('distrito_local')->groupBy('distrito_local')->orderBy('distrito_local')->all(),
            'distrito_local', 'distrito_local');
        $distritosFederales = ArrayHelper::map(
            PREPSeccion::find()->select('distrito_federal')->groupBy('distrito_federal')->orderBy('distrito_federal')->all(),
            'distrito_federal', 'distrito_federal');
        $candidatos = [];
        $zonas = [];
        $secciones = [];
        $whereZonaSeccion = '';

        if (Yii::$app->request->post('tipoEleccion') != null) {
            if (Yii::$app->request->post('municipio') != null) {
                $where = 'municipio = '.(int)Yii::$app->request->post('municipio');
            } else if (Yii::$app->request->post('distritoLocal') != null) {
                $where = 'distrito_local = '.(int)Yii::$app->request->post('distritoLocal');
            } else if (Yii::$app->request->post('distritoFederal') != null) {
                $where = 'distrito_federal = '.(int)Yii::$app->request->post('distritoFederal');
            }

            $whereZonaSeccion = $where;

            if (Yii::$app->request->post('zona')) {
                $whereZonaSeccion .= ' AND zona = '.Yii::$app->request->post('zona');
            }

            if (Yii::$app->request->post('iniSeccion')) {
                $whereZonaSeccion .= ' AND seccion BETWEEN '.Yii::$app->request->post('iniSeccion'). ' AND '.Yii::$app->request->post('finSeccion');
            }

            $candidatos = ArrayHelper::map(PREPCandidato::find()->where($where)->andWhere('activo = 1')->all(), 'id_candidato', 'nombre');

            $zonas = ArrayHelper::map(PREPSeccion::getZonas($where), 'zona', 'zona');
            $listSecciones = PREPSeccion::find()->select('id_seccion, seccion')->where('activo = 1')->orderBy('seccion');

            switch (Yii::$app->request->post('tipoEleccion')) {
                case '1': // Presidencia Municipal
                    $listSecciones->andWhere('municipio = '.Yii::$app->request->post('municipio'));
                    break;
                case '2': // Diputación Local
                    $listSecciones->andWhere('distrito_local = '.Yii::$app->request->post('distritoLocal'));
                    break;
                case '3': // Diputación Federal
                    $listSecciones->andWhere('distrito_federal = '.Yii::$app->request->post('distritoFederal'));
                    break;
            }

            if (Yii::$app->request->post('zona')) {
                $listSecciones->andWhere('zona = '.Yii::$app->request->post('zona'));
            }

            $secciones = ArrayHelper::map($listSecciones->all(), 'seccion', 'seccion');
        }

        return $this->render('index', [
            'tiposEleccion' => $tiposEleccion,
            'municipios' => $municipios,
            'distritosLocales' => $distritosLocales,
            'distritosFederales' => $distritosFederales,
            'candidatos' => $candidatos,
            'zonas' => $zonas,
            'secciones' => $secciones,
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

        $fechaCorte = '';

        if (Yii::$app->request->post('fechaCorte') != '') {
            $fechaHora = explode(' ', Yii::$app->request->post('fechaCorte'));
            $fecha = explode('-', $fechaHora[0]);
            $fechaCorte = $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.($fechaHora[1]=='' ? '00:00' : $fechaHora[1]).':00.000';
        }

        $result = PREPVoto::getResultados(
            $nameColum,
            (int)$valueColum,
            (int)Yii::$app->request->post('zona'),
            (int)Yii::$app->request->post('iniSeccion'),
            (int)Yii::$app->request->post('finSeccion'),
            $fechaCorte);

        //$candidatos = PREPCandidato::find()->
        //select('PREP_Candidato.id_candidato, PREP_Candidato.id_partido, PREP_Candidato.nombre, PREP_Partido.color')
        //->joinWith('partido')->orderBy('id_candidato')
        //->where($nameColum.'='.(int)$valueColum)
        //->andWhere('activo = 1')->all();
        $candidatos = Yii::$app->db->createCommand('SELECT
                [PREP_Candidato].[id_candidato]
                ,[PREP_Candidato].[id_partido]
                ,[PREP_Candidato].[nombre]
                ,[PREP_Partido].[color]
            FROM [PREP_Candidato]
            INNER JOIN [PREP_Partido] ON
                [PREP_Partido].[id_partido] = [PREP_Candidato].[id_partido]
            WHERE '.$nameColum.'='.(int)$valueColum.
                ' AND activo = 1'.
            ' ORDER BY id_partido')->queryAll();

        $resultados = [];
        $totalCasillas = PREPVoto::getCountTotalCasillas(
            $nameColum,
            (int)$valueColum,
            (int)Yii::$app->request->post('zona'),
            (int)Yii::$app->request->post('iniSeccion'),
            (int)Yii::$app->request->post('finSeccion'));
        $casillasConsideradas = PREPVoto::getCountCasillasConsideradas(
            $nameColum,
            (int)$valueColum,
            (int)Yii::$app->request->post('zona'),
            (int)Yii::$app->request->post('iniSeccion'),
            (int)Yii::$app->request->post('finSeccion'),
            $fechaCorte);

        foreach ($result as $fila) {
            $resultados[$fila['seccion']]['seccion'] = $fila['seccion'];
            $resultados[$fila['seccion']]['meta'] = $fila['meta'];
            $resultados[$fila['seccion']]['votos'][$fila['id_candidato']] = $fila['no_votos'];
        }

        $respuesta = [
            'resultado' => $resultados,
            'candidatos' => $candidatos,
            'totalCasillas' => $totalCasillas,
            'casillasConsideradas' => $casillasConsideradas,
        ];

        return $respuesta;
    }
}
