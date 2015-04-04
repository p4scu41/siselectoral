<?php

namespace app\controllers;

use Yii;
use app\models\Organizaciones;
use app\models\OrganizacionesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * OrganizacionesController implements the CRUD actions for Organizaciones model.
 */
class OrganizacionesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'integrantes', 'delintegrante', 'addintegrante'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'integrantes', 'delintegrante', 'addintegrante'],
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
     * Lists all Organizaciones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrganizacionesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Organizaciones model.
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
     * Creates a new Organizaciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Organizaciones();
        $dependencias = Organizaciones::getDependencias();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->IdOrganizacion]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'municipios' => $dependencias['municipios'],
                'tipos' => $dependencias['tipos'],
            ]);
        }
    }

    /**
     * Updates an existing Organizaciones model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dependencias = Organizaciones::getDependencias();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->IdOrganizacion]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'municipios' => $dependencias['municipios'],
                'tipos' => $dependencias['tipos'],
            ]);
        }
    }

    /**
     * Deletes an existing Organizaciones model.
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
     * Finds the Organizaciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Organizaciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Organizaciones::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionIntegrantes($idOrg)
    {
        $model = $this->findModel($idOrg);
        $integrantes = Organizaciones::listIntegrantes($idOrg);
        $dependencias = Organizaciones::getDependencias();

        return $this->render('integrantes', [
                'model' => $model,
                'integrantes' => $integrantes,
                'municipios' => $dependencias['municipios'],
            ]);
    }

    public function actionDelintegrante()
    {
        $idOrg = Yii::$app->request->post('org');
        $idInte = Yii::$app->request->post('inte');
        $response = ['error'=>false, 'mensaje'=>'Integrante eliminado exitosamente'];

        try {
            if (!Yii::$app->getRequest()->validateCsrfToken()) {
                throw new Exception('Error CSRF');
            }
            Organizaciones::delIntegrante($idOrg, $idInte);
        } catch (Exception $ex) {
            $response = ['error'=>true, 'mensaje'=>'Error al eliminar el Integrante'];
        }

        return json_encode($response);
    }

    public function actionAddintegrante()
    {
        $idOrg = Yii::$app->request->post('org');
        $idInte = Yii::$app->request->post('inte');
        $response = ['error'=>false, 'mensaje'=>'Integrante agregado exitosamente'];

        try {
            $integrante = Organizaciones::AddIntegrante($idOrg, $idInte);

            $response['integrante'] = $integrante;
        } catch (Exception $ex) {
            $response = ['error'=>true, 'mensaje'=>'Error al agregar el Integrante'];
        }

        return json_encode($response);
    }
}
