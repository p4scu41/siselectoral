<?php

namespace app\controllers;

use Yii;
use app\models\PREPSeccion;
use app\models\PREPSeccionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\helpers\MunicipiosUsuario;

/**
 * PrepseccionController implements the CRUD actions for PREPSeccion model.
 */
class PrepseccionController extends Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Sección';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Secciones';
    
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
     * Lists all PREPSeccion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PREPSeccionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $municipios = MunicipiosUsuario::getMunicipios();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
            'municipios' => $municipios,
        ]);
    }

    /**
     * Displays a single PREPSeccion model.
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
     * Creates a new PREPSeccion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PREPSeccion();
        $model->activo = 1;
        $municipios = MunicipiosUsuario::getMunicipios();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id_seccion]);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
                'municipios' => $municipios,
            ]);
        }
    }

    /**
     * Updates an existing PREPSeccion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $municipios = MunicipiosUsuario::getMunicipios();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id_seccion]);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
                'municipios' => $municipios,
            ]);
        }
    }

    /**
     * Deletes an existing PREPSeccion model.
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
     * Finds the PREPSeccion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PREPSeccion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PREPSeccion::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('La página solicitada no existe.');
        }
    }

    public function actionGetbymuni()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $secciones = PREPSeccion::getByMunicipio(Yii::$app->request->post('muni'));

        return $secciones;
    }

    public function actionGetbyattr()
    {
        Yii::$app->getResponse()->format = \yii\web\Response::FORMAT_JSON;

        $secciones = PREPSeccion::find()->select('id_seccion, seccion')->where('activo = 1')->orderBy('seccion');

        switch (Yii::$app->request->post('tipoEleccion')) {
            case '1': // Presidencia Municipal
                $secciones->andWhere('municipio = '.Yii::$app->request->post('municipio'));
                break;
            case '2': // Diputación Local
                $secciones->andWhere('distrito_local = '.Yii::$app->request->post('distritoLocal'));
                break;
            case '3': // Diputación Federal
                $secciones->andWhere('distrito_federal = '.Yii::$app->request->post('distritoFederal'));
                break;
            default:
                return [];
        }

        $result = $secciones->all();

        return $result;
    }
}
