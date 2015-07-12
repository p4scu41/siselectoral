<?php

namespace app\controllers;

use Yii;
use app\models\PREPCandidato;
use app\models\PREPPartido;
use app\models\PREPCandidatoSearch;
use app\models\PREPTipoEleccion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\helpers\MunicipiosUsuario;
use app\helpers\PerfilUsuario;

/**
 * PrepcandidatoController implements the CRUD actions for PREPCandidato model.
 */
class PrepcandidatoController extends Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Candidato';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Candidatos';
    
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
     * Lists all PREPCandidato models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!PerfilUsuario::hasPermiso('1fdee8d8-ef29-4966-badf-3a796b0e1570', 'R')) {
            return $this->redirect(['site/index']);
        }

        $searchModel = new PREPCandidatoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    /**
     * Displays a single PREPCandidato model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    /**
     * Creates a new PREPCandidato model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PREPCandidato();
        $model->activo = 1;
        $municipios = MunicipiosUsuario::getMunicipios();
        $partidos = ArrayHelper::map(PREPPartido::find()->select('id_partido, nombre_corto')->all(), 'id_partido', 'nombre_corto');
        $tiposEleccion = ArrayHelper::map(PREPTipoEleccion::find()->select('id_tipo_eleccion, descripcion')->all(), 'id_tipo_eleccion', 'descripcion');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id_candidato]);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
                'municipios' => $municipios,
                'partidos' => $partidos,
                'tiposEleccion' => $tiposEleccion,
            ]);
        }
    }

    /**
     * Updates an existing PREPCandidato model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $municipios = MunicipiosUsuario::getMunicipios();
        $partidos = ArrayHelper::map(PREPPartido::find()->select('id_partido, nombre_corto')->all(), 'id_partido', 'nombre_corto') ;
        $tiposEleccion = ArrayHelper::map(PREPTipoEleccion::find()->select('id_tipo_eleccion, descripcion')->all(), 'id_tipo_eleccion', 'descripcion') ;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id_candidato]);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
                'municipios' => $municipios,
                'partidos' => $partidos,
                'tiposEleccion' => $tiposEleccion,
            ]);
        }
    }

    /**
     * Deletes an existing PREPCandidato model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PREPCandidato model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PREPCandidato the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PREPCandidato::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('La página solicitada no existe.');
        }
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public function actionGet()
    {
        Yii::$app->getResponse()->format = \yii\web\Response::FORMAT_JSON;

        $candidatos = PREPCandidato::find()->select('id_candidato, nombre')->where('activo = 1')->orderBy('nombre');

        switch (Yii::$app->request->post('tipoEleccion')) {
            case '1': // Presidencia Municipal
                $candidatos->andWhere('municipio = '.Yii::$app->request->post('municipio'));
                break;
            case '2': // Diputación Local
                $candidatos->andWhere('distrito_local = '.Yii::$app->request->post('distritoLocal'));
                break;
            case '3': // Diputación Federal
                $candidatos->andWhere('distrito_federal = '.Yii::$app->request->post('distritoFederal'));
                break;
            default:
                return [];
        }

        $result = $candidatos->all();

        return $result;
    }
}
