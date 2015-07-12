<?php

namespace app\controllers;

use Yii;
use app\models\PREPCasillaSeccion;
use app\models\PREPCasillaSeccionSearch;
use app\models\PREPCasilla;
use app\models\PREPSeccion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\helpers\MunicipiosUsuario;
use app\helpers\PerfilUsuario;

/**
 * PrepcasillaseccionController implements the CRUD actions for PREPCasillaSeccion model.
 */
class PrepcasillaseccionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'getfromseccion', 'observacion'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'getfromseccion', 'observacion'],
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
     * Lists all PREPCasillaSeccion models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!PerfilUsuario::hasPermiso('1fdee8d8-ef29-4966-badf-3a796b0e1570', 'R')) {
            return $this->redirect(['site/index']);
        }

        $searchModel = new PREPCasillaSeccionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PREPCasillaSeccion model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PREPCasillaSeccion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PREPCasillaSeccion();
        $municipios = MunicipiosUsuario::getMunicipios();
        $secciones = [];
        $casillas = ArrayHelper::map(PREPCasilla::find()->all(), 'id_casilla', 'descripcion');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id_casilla_seccion]);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'municipios' => $municipios,
                'id_municipio' => 0,
                'casillas' => $casillas,
                'secciones' => $secciones
            ]);
        }
    }

    /**
     * Updates an existing PREPCasillaSeccion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $municipios = MunicipiosUsuario::getMunicipios();
        $seccion = PREPSeccion::findOne($model->id_seccion);
        $secciones = ArrayHelper::map(PREPSeccion::getByMunicipio($seccion->municipio), 'id_seccion', 'seccion');
        $casillas = ArrayHelper::map(PREPCasilla::find()->all(), 'id_casilla', 'descripcion');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id_casilla_seccion]);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'municipios' => $municipios,
                'id_municipio' => $seccion->municipio,
                'casillas' => $casillas,
                'secciones' => $secciones
            ]);
        }
    }

    /**
     * Deletes an existing PREPCasillaSeccion model.
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
     * Finds the PREPCasillaSeccion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PREPCasillaSeccion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PREPCasillaSeccion::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetfromseccion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id_nodo = Yii::$app->request->post('nodo');
        $nodo = \app\models\DetalleEstructuraMovilizacion::findOne($id_nodo);
        $seccion = \app\models\DetalleEstructuraMovilizacion::getSeccionNodo($id_nodo);

        $casillas = PREPCasillaSeccion::getFromSeccion($nodo->Municipio, $seccion);

        return $casillas;
    }
    
    public function actionObservacion()
    {
        $model = $this->findModel(Yii::$app->request->post('casilla'));
        
        $model->observaciones = Yii::$app->request->post('obser');
        $model->save();
        
        return true;
    }

}
