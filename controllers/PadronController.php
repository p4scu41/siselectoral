<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\PadronGlobal;
use app\models\PadronGlobalSearch;
use app\models\DetalleEstructuraMovilizacion;
use app\models\Puestos;
use app\models\CMunicipio;
use app\models\ElementoCatalogo;
use app\models\Cardex;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\helpers\ResizeImage;
use app\helpers\MunicipiosUsuario;
use yii\helpers\Url;
use yii\web\UploadedFile;
use app\models\SeguimientoCambios;
use app\helpers\PerfilUsuario;

/**
 * PadronController implements the CRUD actions for PadronGlobal model.
 */
class PadronController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['get', 'persona', 'buscar', 'buscarajax', 'find', 'view', 'create', 'update', 'delete', 'findbyclaveelectoral'],
                'rules' => [
                    [
                        'actions' => ['get', 'persona', 'buscar', 'buscarajax', 'find', 'view', 'create', 'update', 'delete', 'findbyclaveelectoral'],
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
                $no_meta_proyec = DetalleEstructuraMovilizacion::getMetaByPromotor($estructura->IdNodoEstructuraMov, $estructura->IdPuesto == 7);
            }

            $no_meta_promocion = DetalleEstructuraMovilizacion::getAvanceMeta($estructura->IdNodoEstructuraMov);
        }

        return $this->render('persona', [
            'persona' => $persona,
            'puesto' => $puestoPersona,
            'dependientes' => $dependientes,
            'jefe' => $jefe,
            'numDepend' => $numDepend,
            'secciones' => isset($secciones) ? $secciones : '',
            'no_meta_estruc' => $no_meta_estruc,
            'no_meta_proyec' => $no_meta_proyec,
            'no_meta_promocion' => $no_meta_promocion,
            'actionPersona' => isset($actionPersona) ? $actionPersona : '',
            'idPuesto' => $IdPuesto,
            'nodo' => $IdNodo,
        ]);
    }


    /**
     * Lists all PadronGlobal models.
     * @return mixed
     */
    public function actionBuscar()
    {
        if (!PerfilUsuario::hasPermiso('36b78aa7-3642-489f-975a-a7213937af74', 'R')) {
            return $this->redirect(['site/index']);
        }

        $searchModel = new PadronGlobalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $municipios = MunicipiosUsuario::getMunicipios();

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
     * Lists all PadronGlobal models.
     * 
     * @return mixed
     */
    public function actionFind()
    {
        $where = '1=1 ';
        $municipio = Yii::$app->request->post('municipio');
        $seccion = Yii::$app->request->post('seccion');
        $apellidoPaterno = Yii::$app->request->post('apellidoPaterno');
        $apellidoMaterno = Yii::$app->request->post('apellidoMaterno');
        $nombre = Yii::$app->request->post('nombre');

        if ($municipio) {
            $where .= ' AND MUNICIPIO = '.$municipio;
        }

        if ($seccion) {
            $where .= ' AND SECCION = '. $seccion;
        }

        if ($apellidoPaterno) {
            $where .= ' AND APELLIDO_PATERNO LIKE \'%'.$apellidoPaterno.'%\'';
        }

        if ($apellidoMaterno) {
            $where .= ' AND APELLIDO_MATERNO LIKE \'%'.$apellidoMaterno.'%\'';
        }

        if ($nombre) {
            $where .=  ' AND NOMBRE LIKE \'%'.$nombre.'%\'';
        }

        $personas = PadronGlobal::find()
            ->select(['CLAVEUNICA', 'REPLACE([NOMBRE], \'\\\', \'Ñ\') AS NOMBRE', 'REPLACE([APELLIDO_PATERNO], \'\\\', \'Ñ\') AS APELLIDO_PATERNO', 'REPLACE([APELLIDO_MATERNO], \'\\\', \'Ñ\') AS APELLIDO_MATERNO', 'SECCION', 'CASILLA', 'DOMICILIO', 'NOM_LOC'])
            ->where($where)
            ->orderBy('APELLIDO_PATERNO, APELLIDO_MATERNO, NOMBRE')
            ->asArray()
            ->all();

        return json_encode($personas);
    }

    /**
     * Displays a single PadronGlobal model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!PerfilUsuario::hasPermiso('36b78aa7-3642-489f-975a-a7213937af74', 'R')) {
            return $this->redirect(['site/index']);
        }

        $model = $this->findModel($id);
        $observacion = Cardex::find()->where('CLAVEUNICA = \''.$model->CLAVEUNICA.'\'')->one();

        if ($observacion) {
            $observacion = $observacion->Nota;
        }

        return $this->render('view', [
            'model' => $model,
            'observacion' => $observacion
        ]);
    }

    /**
     * Creates a new PadronGlobal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!PerfilUsuario::hasPermiso('36b78aa7-3642-489f-975a-a7213937af74', 'C')) {
            return $this->redirect(['site/index']);
        }

        $model = new PadronGlobal();
        $municipios = MunicipiosUsuario::getMunicipios();
        
        $escolaridad = ArrayHelper::map(
                ElementoCatalogo::find()->where(['IdTipoCatalogo'=>1])
                ->select(['IdElementoCatalogo', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(),
            'IdElementoCatalogo',
            'Descripcion'
        );
        $ocupacion = ArrayHelper::map(
                ElementoCatalogo::find()->where(['IdTipoCatalogo'=>2])
                ->select(['IdElementoCatalogo', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(),
            'IdElementoCatalogo',
            'Descripcion'
        );
        $estado_civil = ArrayHelper::map(
                ElementoCatalogo::find()->where(['IdTipoCatalogo'=>3])
                ->select(['IdElementoCatalogo', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(), 'IdElementoCatalogo', 'Descripcion'
        );

        if (Yii::$app->request->isPost) {
            $model->CLAVEUNICA = PadronGlobal::newUID();
            $model->CONS_ALF_POR_SECCION = 0;
            $model->FECHA_NACI_CLAVE_ELECTORAL = substr(Yii::$app->request->post('PadronGlobal')['ALFA_CLAVE_ELECTORAL'], -12, 6);
            $model->LUGAR_NACIMIENTO = substr(Yii::$app->request->post('PadronGlobal')['ALFA_CLAVE_ELECTORAL'], -6, 2);
            $model->DIGITO_VERIFICADOR = substr(Yii::$app->request->post('PadronGlobal')['ALFA_CLAVE_ELECTORAL'], -3, 1);
            $model->CLAVE_HOMONIMIA = substr(Yii::$app->request->post('PadronGlobal')['ALFA_CLAVE_ELECTORAL'], -2, 2);
            $model->CALLE = $model->DOMICILIO.(!empty($model->NUM_EXTERIOR) ? $model->NUM_EXTERIOR : ', '.$model->NUM_EXTERIOR).', CP '.$model->CODIGO_POSTAL.', '.$model->DES_LOC.' '.$model->NOM_LOC.', '.
            $model->AGREGADO = 1;
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $pathFoto = Url::to('@app/fotos', true).'/'.strtolower($model->CLAVEUNICA).'.jpg';
            $model->foto = UploadedFile::getInstance($model, 'foto');
            if ($model->foto && $model->validate()) {
                $model->foto->saveAs($pathFoto);
                list($ancho, $alto) = getimagesize($pathFoto);

                if ($ancho > 200 || $alto > 250) {
                     // Redimensionar
                    ResizeImage::smart_resize_image($pathFoto, null, 200, 250, false , $pathFoto, false, false, 100);
                }
            }
            
            if (Yii::$app->request->post('observacion') != null) {
                $observacion = new Cardex();
                
                $observacion->CLAVEUNICA = $model->CLAVEUNICA;
                $observacion->Nota = Yii::$app->request->post('observacion');
                $observacion->FechaInsercion = date('Y-m-d H:i:s');
                $observacion->FechaActualizacion = date('Y-m-d H:i:s');
                $observacion->usuarioactualiza = Yii::$app->user->identity->persona->CLAVEUNICA;
                $observacion->save();
            }

            $log = new SeguimientoCambios();
            $log->usuario = Yii::$app->user->identity->IdUsuario;
            $log->tabla = 'PadronGlobal';
            $log->campo = 'CLAVEUNICA';
            $log->nuevo_valor = $model->CLAVEUNICA;
            $log->accion = SeguimientoCambios::INSERT;
            $log->fecha = date('Y-m-d H:i:s');
            $log->save();

            return $this->redirect(['view', 'id' => $model->CLAVEUNICA]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'municipios' => $municipios,
                'escolaridad' => $escolaridad,
                'ocupacion' => $ocupacion,
                'estado_civil' => $estado_civil
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
        if (!PerfilUsuario::hasPermiso('36b78aa7-3642-489f-975a-a7213937af74', 'U')) {
            return $this->redirect(['site/index']);
        }

        $model = $this->findModel($id);
        $municipios = MunicipiosUsuario::getMunicipios();
        
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
            $pathFoto = Url::to('@app/fotos', true).'/'.strtolower($model->CLAVEUNICA).'.jpg';
            $model->foto = UploadedFile::getInstance($model, 'foto');

            if ($model->foto && $model->validate()) {
                $model->foto->saveAs($pathFoto);

                list($ancho, $alto) = getimagesize($pathFoto);

                if ($ancho > 200 || $alto > 250) {
                     // Redimensionar
                    ResizeImage::smart_resize_image($pathFoto, null, 200, 250, false , $pathFoto, false, false, 100);
                }
            }

            // Guarda la observacion
            if (Yii::$app->request->post('observacion') != null) {
                $observacion = Cardex::find()->where('CLAVEUNICA = \''.$model->CLAVEUNICA.'\'')->one();

                if ($observacion == null) {
                    $observacion = new Cardex();
                }
                
                $observacion->CLAVEUNICA = $model->CLAVEUNICA;
                $observacion->Nota = Yii::$app->request->post('observacion');
                $observacion->FechaInsercion = date('Y-m-d H:i:s');
                $observacion->FechaActualizacion = date('Y-m-d H:i:s');
                $observacion->usuarioactualiza = Yii::$app->user->identity->persona->CLAVEUNICA;
                $observacion->save();
            }


            $log = new SeguimientoCambios();
            $log->usuario = Yii::$app->user->identity->IdUsuario;
            $log->tabla = 'PadronGlobal';
            $log->accion = SeguimientoCambios::UPDATE;
            $log->fecha = date('Y-m-d H:i:s');
            $log->detalles = json_encode($model->attributes);
            $log->save();

            return $this->redirect(['view', 'id' => $model->CLAVEUNICA]);
        } else {
        $observacion = Cardex::find()->where('CLAVEUNICA = \''.$model->CLAVEUNICA.'\'')->one();

        if ($observacion) {
            $observacion = $observacion->Nota;
        }
            return $this->render('update', [
                'model' => $model,
                'municipios' => $municipios,
                'escolaridad' => $escolaridad,
                'ocupacion' => $ocupacion,
                'estado_civil' => $estado_civil,
                'observacion' => $observacion,
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
        if (!PerfilUsuario::hasPermiso('36b78aa7-3642-489f-975a-a7213937af74', 'D')) {
            return $this->redirect(['site/index']);
        }

        $model = $this->findModel($id);

        $log = new SeguimientoCambios();
        $log->usuario = Yii::$app->user->identity->IdUsuario;
        $log->tabla = 'PadronGlobal';
        $log->accion = SeguimientoCambios::DELETE;
        $log->fecha = date('Y-m-d H:i:s');
        $log->detalles = json_encode($model->attributes);

        $model->delete();
        $log->save();

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

    /**
     * Lists all PadronGlobal models.
     *
     * @return mixed
     */
    public function actionFindbyclaveelectoral()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $clave = Yii::$app->request->post('clave');

        $personas = PadronGlobal::find()
            ->select('*')
            ->where('ALFA_CLAVE_ELECTORAL = \''.$clave.'\'')
            ->andWhere('[MUNICIPIO] IN ('.implode(',', array_keys(MunicipiosUsuario::getMunicipios())).')')
            ->asArray()
            ->all();

        return $personas;
    }
}
