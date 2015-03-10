<?php

namespace app\controllers;

use Yii;
use app\models\PadronGlobal;
use app\models\PadronGlobalSearch;
use app\models\DetalleEstructuraMovilizacion;
use app\models\Puestos;
use app\models\CMunicipio;
use app\models\ElementoCatalogo;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\helpers\ResizeImage;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * PadronController implements the CRUD actions for PadronGlobal model.
 */
class PadronController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Obtiene los datos de una persona
     *
     * @param uniqueidentifier $id ID del padron
     * @return JSON Datos de la persona
     */
    public function actionGet($id)
    {
        try {
            $objPersona = PadronGlobal::findOne(['CLAVEUNICA'=>$id]);
            $persona = null;

            if ($objPersona != null) {
                $persona = $objPersona->attributes;
                $persona['foto'] = $objPersona->getFoto();
            }

            return json_encode($persona);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Muestra los datos de la persona en un puesto determinado
     *
     * @param UID $id Identificador de la persona
     * @return View Vista con detalles de la persona
     */
    public function actionPersona()
    {
        if ( stripos(Yii::$app->request->referrer, 'site/positiontree')!== false ) {
            Yii::$app->session->set('arrayHistory', []);
            Yii::$app->session->set('firsTime', true);
        }

        if (Yii::$app->request->post('back')) {
            $arrayHistory = Yii::$app->session->get('arrayHistory');

            // Si es la primera vez, eliminamos el ultimo elemento ya que es el actual
            if (Yii::$app->session->get('firsTime')) {
                array_pop($arrayHistory);
            }

            $id = array_pop($arrayHistory);
            Yii::$app->session->set('arrayHistory', $arrayHistory);

            if( !$id ) {
                return $this->redirect(['site/positiontree']);
            } else {
                $actionPersona = Url::toRoute('padron/persona', true);
            }
            Yii::$app->session->set('firsTime', false);
        } else {
            $arrayHistory = Yii::$app->session->get('arrayHistory');
            array_push($arrayHistory, Yii::$app->request->post('id'));
            Yii::$app->session->set('arrayHistory', $arrayHistory);
            $id = Yii::$app->request->post('id');
        }

        //$id = Yii::$app->request->post('id');
        $persona = PadronGlobal::findOne(['CLAVEUNICA'=>$id]);
        $estructura = DetalleEstructuraMovilizacion::findOne(['IdPersonaPuesto' => $id]);
        $no_meta_estruc = 0;
        $no_meta_proyec = 0;
        $no_meta_promocion = 0;
        $IdPuesto = 0;
        $IdNodo = 0;

        if($estructura != null) {
            $puesto = Puestos::findOne(['IdPuesto' => $estructura->IdPuesto]);
            $IdPuesto = $estructura->IdPuesto;
            $IdNodo = $estructura->IdNodoEstructuraMov;

            $puestoPersona = $puesto->Descripcion.' - '.$estructura->Descripcion;

            $dependientes = $estructura->getDependientes();

            if($estructura->IdPuesto == 4) {
                $secciones = $estructura->getSecciones();
            }
            $jefe = $estructura->getJefe();
            $numDepend = $estructura->getCountDepen();
            $no_meta_estruc = DetalleEstructuraMovilizacion::getResumenNodo($estructura->IdNodoEstructuraMov);

            if (count($no_meta_estruc)) {
                $no_meta_estruc = $no_meta_estruc[count($no_meta_estruc)-2]['Avances %'];
            }

            if ($estructura->IdPuesto <= 5) {
                $no_meta_proyec = DetalleEstructuraMovilizacion::getMetaBySeccion($estructura->IdNodoEstructuraMov);
            } else {
                $no_meta_proyec = DetalleEstructuraMovilizacion::getMetaByPromotor($estructura->IdNodoEstructuraMov);
            }

            $no_meta_promocion = DetalleEstructuraMovilizacion::getAvanceMeta($estructura->IdNodoEstructuraMov);
        }

        return $this->render('persona', [
            'persona' => $persona,
            'puesto' => $puestoPersona,
            'dependientes' => $dependientes,
            'jefe' => $jefe,
            'numDepend' => $numDepend,
            'secciones' => $secciones,
            'no_meta_estruc' => $no_meta_estruc,
            'no_meta_proyec' => $no_meta_proyec,
            'no_meta_promocion' => $no_meta_promocion,
            'actionPersona' => $actionPersona,
            'puesto' => $IdPuesto,
            'nodo' => $IdNodo,
        ]);
    }


    /**
     * Lists all PadronGlobal models.
     * @return mixed
     */
    public function actionBuscar()
    {
        $searchModel = new PadronGlobalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (strtolower(Yii::$app->user->identity->perfil->IdPerfil) == strtolower(Yii::$app->params['idAdmin'])) {
            $listMunicipios = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all();
        } else {
            $listMunicipios = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->where(['IdMunicipio'=>Yii::$app->user->identity->persona->MUNICIPIO])
                ->orderBy('DescMunicipio')
                ->all();
        }

        $municipios = ArrayHelper::map($listMunicipios, 'IdMunicipio', 'DescMunicipio');

        return $this->render('buscar', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'municipios' => $municipios,

        ]);
    }

    /**
     * Lists all PadronGlobal models.
     * @return mixed
     */
    public function actionBuscarajax()
    {
        $searchModel = new PadronGlobalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $models = $dataProvider->getModels();
        $datos = [];

        foreach ($models as $model) {
            array_push($datos, $model->attributes);
        }

        print_r(json_encode($datos));

        //return ;
    }

    /**
     * Displays a single PadronGlobal model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PadronGlobal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PadronGlobal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->CLAVEUNICA]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PadronGlobal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $municipios = ArrayHelper::map(
            CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all(), 'IdMunicipio', 'DescMunicipio'
        );
        $escolaridad = ArrayHelper::map(
                ElementoCatalogo::find()->where(['IdTipoCatalogo'=>1])
                ->select(['IdElementoCatalogo', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(), 'IdElementoCatalogo', 'Descripcion'
        );
        $ocupacion = ArrayHelper::map(
                ElementoCatalogo::find()->where(['IdTipoCatalogo'=>2])
                ->select(['IdElementoCatalogo', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(), 'IdElementoCatalogo', 'Descripcion'
        );
        $estado_civil = ArrayHelper::map(
                ElementoCatalogo::find()->where(['IdTipoCatalogo'=>3])
                ->select(['IdElementoCatalogo', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(), 'IdElementoCatalogo', 'Descripcion'
        );

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $pathFoto = Url::to('@app/fotos', true).'/'.$model->CLAVEUNICA.'.jpg';
            $model->foto = UploadedFile::getInstance($model, 'foto');

            if ($model->foto && $model->validate()) {
                $model->foto->saveAs($pathFoto);

                list($ancho, $alto) = getimagesize($pathFoto);

                if ($ancho > 200 || $alto > 250) {
                     // Redimensionar
                    ResizeImage::smart_resize_image($pathFoto, null, 200, 250, false , $pathFoto, false, false, 100);
                }
            }

            return $this->redirect(['view', 'id' => $model->CLAVEUNICA]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'municipios' => $municipios,
                'escolaridad' => $escolaridad,
                'ocupacion' => $ocupacion,
                'estado_civil' => $estado_civil
            ]);
        }
    }

    /**
     * Deletes an existing PadronGlobal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PadronGlobal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PadronGlobal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PadronGlobal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('La página solicitada no existe');
        }
    }
}
