<?php

namespace app\controllers;

use Yii;
use app\models\PREPVoto;
use app\models\PREPVotoSearch;
use app\models\PREPTipoEleccion;
use app\models\PREPSeccion;
use app\models\PREPCandidato;
use app\models\PREPCasillaSeccion;
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
class PrepvotoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'votar'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'votar'],
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
     * Lists all PREPVoto models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!PerfilUsuario::hasPermiso('7b8b4d91-2b1d-4a65-9f29-a4c2f22c9b8f', 'R')) {
            return $this->redirect(['site/index']);
        }

        $searchModel = new PREPVotoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $tiposEleccion = ArrayHelper::map(PREPTipoEleccion::find()->select('id_tipo_eleccion, descripcion')->all(), 'id_tipo_eleccion', 'descripcion');
        $municipios = MunicipiosUsuario::getMunicipios();
        $distritosLocales = ArrayHelper::map(
            PREPSeccion::find()->select('distrito_local')->groupBy('distrito_local')->orderBy('distrito_local')->all(),
            'distrito_local', 'distrito_local');
        $distritosFederales = ArrayHelper::map(
            PREPSeccion::find()->select('distrito_federal')->groupBy('distrito_federal')->orderBy('distrito_federal')->all(),
            'distrito_federal', 'distrito_federal');
        $where = null;
        $whereZonaSeccion = '';
        $candidatos = [];
        $casillas = [];
        $votos = [];
        $zonas = [];
        $secciones = [];

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

            $candidatos = PREPCandidato::find()->where($where)->andWhere('activo = 1')->orderBy('orden')->all();
            $casillas = PREPCasillaSeccion::getWhere($whereZonaSeccion);

            if (!empty($candidatos)) {
                $resultVotos = PREPVoto::getByCandidatos(ArrayHelper::map($candidatos, 'id_candidato', 'id_candidato'));
            }

            if (count($resultVotos)) {
                foreach ($resultVotos as $filaVoto) {
                    $votos[$filaVoto['id_candidato']][$filaVoto['id_casilla_seccion']] = $filaVoto['no_votos'];
                }
            }

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
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tiposEleccion' => $tiposEleccion,
            'municipios' => $municipios,
            'distritosLocales' => $distritosLocales,
            'distritosFederales' => $distritosFederales,
            'candidatos' => $candidatos,
            'casillas' => $casillas,
            'votos' => $votos,
            'zonas' => $zonas,
            'secciones' => $secciones,
        ]);
    }

    /**
     * Displays a single PREPVoto model.
     * @param integer $id_partido
     * @param integer $id_casilla_seccion
     * @return mixed
     */
    public function actionView($id_partido, $id_casilla_seccion)
    {
        return $this->render('view', [
            'model' => $this->findModel($id_partido, $id_casilla_seccion),
        ]);
    }

    /**
     * Creates a new PREPVoto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PREPVoto();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_partido' => $model->id_partido, 'id_casilla_seccion' => $model->id_casilla_seccion]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PREPVoto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id_partido
     * @param integer $id_casilla_seccion
     * @return mixed
     */
    public function actionUpdate($id_partido, $id_casilla_seccion)
    {
        $model = $this->findModel($id_partido, $id_casilla_seccion);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_partido' => $model->id_partido, 'id_casilla_seccion' => $model->id_casilla_seccion]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PREPVoto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id_partido
     * @param integer $id_casilla_seccion
     * @return mixed
     */
    public function actionDelete($id_partido, $id_casilla_seccion)
    {
        $this->findModel($id_partido, $id_casilla_seccion)->delete();

        return $this->redirect(['index']);
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

    public function actionVotar()
    {
        PREPVoto::setVoto([
            'id_candidato' => Yii::$app->request->post('candidato'),
            'id_casilla_seccion' => Yii::$app->request->post('casilla'),
            'no_votos' => Yii::$app->request->post('votos'),
        ]);

        return true;
    }
}
