<?php

namespace app\controllers;

use Yii;
use app\models\Promocion;
use app\models\PadronGlobal;
use app\models\PromocionSearch;
use app\models\CMunicipio;
use app\models\DetallePromocion;
use app\models\Reporte;
use kartik\mpdf\Pdf;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\helpers\MunicipiosUsuario;
use app\models\SeguimientoCambios;
use app\helpers\PerfilUsuario;

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
                'only' => ['index', 'view', 'create', 'update', 'delete', 'getlistnodos', 'getorganizaciones', 'pdf', 'findactivistaspromocion', 'findotrospromocion'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'getlistnodos', 'getorganizaciones', 'pdf', 'findactivistaspromocion', 'findotrospromocion'],
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
        if (!PerfilUsuario::hasPermiso('ce7ad335-baee-498f-b4b0-94c283d9701b', 'R')) {
            return $this->redirect(['site/index']);
        }

        $searchModel = new PromocionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $municipios = MunicipiosUsuario::getMunicipios();

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
        $municipios = MunicipiosUsuario::getMunicipios();
        $request = Yii::$app->getRequest()->post('Promocion');

        //print_r(Yii::$app->getRequest()->post());

        if ($request) {
            //die();
            if (!DetallePromocion::existsDetallePromocion($request['IdpersonaPromovida'], $request['IdPersonaPromueve'])) {
                $modelDetalle = new DetallePromocion();
                $modelDetalle->IdPErsonaPromueve = $request['IdPersonaPromueve'];
                $modelDetalle->IdPersonaPromovida = $request['IdpersonaPromovida'];
                $modelDetalle->save();

                $log = new SeguimientoCambios();
                $log->usuario = Yii::$app->user->identity->IdUsuario;
                $log->tabla = 'DetallePromocion';
                $log->detalles = json_encode($modelDetalle->attributes);
                $log->accion = SeguimientoCambios::INSERT;
                $log->fecha = date('Y-m-d H:i:s');
                $log->save();
            }

            if ($existsPromocion = Promocion::existsPromocion($request['IdpersonaPromovida'])) {
                Yii::$app->session->setFlash('existsPromocion', 'La persona "'.$existsPromocion->personaPromovida->nombreCompleto.'" ya ha sido promovido por "'.$existsPromocion->personaPromueve->nombreCompleto.'"');
                return $this->redirect(['index']);
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $log = new SeguimientoCambios();
            $log->usuario = Yii::$app->user->identity->IdUsuario;
            $log->tabla = 'Promocion';
            $log->detalles = json_encode($model->attributes);
            $log->accion = SeguimientoCambios::INSERT;
            $log->fecha = date('Y-m-d H:i:s');
            $log->save();

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
        $municipios = MunicipiosUsuario::getMunicipios();

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
        $filtros['municipio'] = Yii::$app->getRequest()->post('municipio');
        $filtros['seccion'] = (int) Yii::$app->getRequest()->post('seccion');
        $filtros['nombre'] = Yii::$app->getRequest()->post('nombre');
        $filtros['puesto'] = Yii::$app->getRequest()->post('puesto');
        $filtros['id'] = (Yii::$app->getRequest()->post('id') == 'undefined' || Yii::$app->getRequest()->post('id') == 'null') ? null : Yii::$app->getRequest()->post('id');

        $result = Promocion::getListNodos($filtros);

        return json_encode($result);
    }

    public function actionGetorganizaciones()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $result = Promocion::getOrganizaciones(Yii::$app->getRequest()->post('id'));

        return $result;
    }

    public function actionPdf()
    {
        $IdPersonaPromueve = Yii::$app->request->get('PromocionSearch')['IdPersonaPromueve'];
        $personaPromueve = $IdPersonaPromueve ? PadronGlobal::findOne(['CLAVEUNICA'=>$IdPersonaPromueve]) : null;
        $searchModel = new PromocionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(false);
        $nombrePersonaPromueve = !empty($personaPromueve) ? ' de '.$personaPromueve->nombreCompleto : '';
        $nombrePersonaPromueve .= Yii::$app->request->get('PromocionSearch')['personaPromueveZona'] ? ' - Z '. Yii::$app->request->get('PromocionSearch')['personaPromueveZona'] : '';
        $nombrePersonaPromueve .= Yii::$app->request->get('PromocionSearch')['personaPromueveSeccion'] ? ' - S '. Yii::$app->request->get('PromocionSearch')['personaPromueveSeccion'] : '';
        $titulo = 'Avance de la estructura de promoción '.$nombrePersonaPromueve;
        $cont = 1;
        $content = Reporte::arrayToHtml(ArrayHelper::toArray($dataProvider->getModels(), [
                'app\models\Promocion' => [
                    'no',
                    'seccion' => function ($model) {
                        return intval($model->seccion);
                    },
                    /*'IdPersonaPromueve' => function ($model) {
                        return $model->personaPromueve->puesto->Descripcion. ' '. $model->personaPromueve->nombreCompleto;
                    },*/
                    'IdPuesto' => function ($model) {
                        return $model->puesto->Descripcion.' '.$model->personaPuesto->nombreCompleto;;
                    },
                    'IdpersonaPromovida' => function ($model) {
                        return $model->personaPromovida->nombreCompleto;
                    },
                    'FechaPromocion',
                ]
            ]), [1, 2,3,4], 
            [],
            [],
            'table table-condensed table-bordered table-hover',
            'border="1" cellpadding="1" cellspacing="1"',
            ['No.', 'Sección', 'Puesto en donde promueve', 'Persona Promovida', 'Fecha de Promoción']);
        $orientation = Pdf::ORIENT_PORTRAIT;

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_LETTER ,
            'content' => $content,
            'filename' => $titulo.'.pdf',
            'destination' => Pdf::DEST_DOWNLOAD,
            'orientation' => $orientation,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            //'cssFile' => '@web/css/kv-mpdf-bootstrap.css',
            'cssInline' => 'body { font-size: 7px !important; line-height: 1 !important; } '.
                'a { font-size: 6px !important; text-decoration: none; } '.
                '.table-condensed > thead > tr > th, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > tbody > tr > td, .table-condensed > tfoot > tr > td { padding: 3px !important; } '.
                '.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td { line-height: 1 !important; }',
            'options' => [
                'title' => $titulo,
                'subject' => 'SIRECI - '.date("d-m-Y h:i:s A")
            ],
            'defaultFontSize' => 7,
            'methods' => [
                'SetHeader' => ['|'.$titulo.'|'],
                'SetFooter' => ['|Pagina {PAGENO}|'],
                'SetColumns' => [1]
            ]
        ]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');
        $headers->add('Set-Cookie', 'fileDownload=true; path=/');
        $headers->add('Cache-Control', 'max-age=60, must-revalidate');

        $pdfApi = $pdf->getApi();
        $pdfApi->SetProtection(['print']);

        return $pdf->render();
    }

    public function actionFindactivistaspromocion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return Promocion::findActivistasPromocion(Yii::$app->getRequest()->post('nombre'), Yii::$app->getRequest()->post('id'), Yii::$app->getRequest()->post('personaPromueve'));
    }

    public function actionFindotrospromocion()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return Promocion::findOtrosPromocion(Yii::$app->getRequest()->post('id'));
    }
}
