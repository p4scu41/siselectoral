<?php

namespace app\controllers;

use Yii;
use app\models\Promocion;
use app\models\PromocionSearch;
use app\models\CMunicipio;
use app\models\DetallePromocion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * PromocionController implements the CRUD actions for Promocion model.
 */
class PromocionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'getlistnodos'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'getlistnodos'],
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
     * Lists all Promocion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PromocionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $listMunicipios = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all();
        $municipios = ArrayHelper::map($listMunicipios, 'IdMunicipio', 'DescMunicipio');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'municipios' => $municipios
        ]);
    }

    /**
     * Displays a single Promocion model.
     * @param integer $IdEstructuraMov
     * @param string $IdpersonaPromovida
     * @return mixed
     */
    public function actionView($IdEstructuraMov, $IdpersonaPromovida)
    {
        return $this->render('view', [
            'model' => $this->findModel($IdEstructuraMov, $IdpersonaPromovida),
        ]);
    }

    /**
     * Creates a new Promocion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Promocion();
        $listMunicipios = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all();
        $municipios = ArrayHelper::map($listMunicipios, 'IdMunicipio', 'DescMunicipio');
        $request = Yii::$app->getRequest()->post('Promocion');

        if ($request) {
            if (!DetallePromocion::existsDetallePromocion($request['IdpersonaPromovida'], $request['IdPersonaPromueve'])) {
                $modelDetalle = new DetallePromocion();
                $modelDetalle->IdPErsonaPromueve = $request['IdPersonaPromueve'];
                $modelDetalle->IdPersonaPromovida = $request['IdpersonaPromovida'];
                $modelDetalle->save();
            }

            if (Promocion::existsPromocion($request['IdpersonaPromovida'])) {
                return $this->redirect(['index']);
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'IdEstructuraMov' => $model->IdEstructuraMov, 'IdpersonaPromovida' => $model->IdpersonaPromovida]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'municipios' => $municipios,
            ]);
        }
    }

    /**
     * Updates an existing Promocion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $IdEstructuraMov
     * @param string $IdpersonaPromovida
     * @return mixed
     */
    public function actionUpdate($IdEstructuraMov, $IdpersonaPromovida)
    {
        $model = $this->findModel($IdEstructuraMov, $IdpersonaPromovida);
        $listMunicipios = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all();
        $municipios = ArrayHelper::map($listMunicipios, 'IdMunicipio', 'DescMunicipio');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'IdEstructuraMov' => $model->IdEstructuraMov, 'IdpersonaPromovida' => $model->IdpersonaPromovida]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'municipios' => $municipios,
            ]);
        }
    }

    /**
     * Deletes an existing Promocion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $IdEstructuraMov
     * @param string $IdpersonaPromovida
     * @return mixed
     */
    public function actionDelete($IdEstructuraMov, $IdpersonaPromovida)
    {
        $this->findModel($IdEstructuraMov, $IdpersonaPromovida)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Promocion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $IdEstructuraMov
     * @param string $IdpersonaPromovida
     * @return Promocion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($IdEstructuraMov, $IdpersonaPromovida)
    {
        if (($model = Promocion::findOne(['IdEstructuraMov' => $IdEstructuraMov, 'IdpersonaPromovida' => $IdpersonaPromovida])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetlistnodos()
    {
        $filtros['municipio'] = (int) Yii::$app->getRequest()->post('municipio');
        $filtros['seccion'] = (int) Yii::$app->getRequest()->post('seccion');
        $filtros['nombre'] = Yii::$app->getRequest()->post('nombre');
        $filtros['puesto'] = Yii::$app->getRequest()->post('puesto');
        $filtros['id'] = (Yii::$app->getRequest()->post('id') == 'undefined' || Yii::$app->getRequest()->post('id') == 'null') ? null : Yii::$app->getRequest()->post('id');

        $result = Promocion::getListNodos($filtros);

        return json_encode($result);
    }
}