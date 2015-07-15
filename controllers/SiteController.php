<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\helpers\MunicipiosUsuario;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\CMunicipio;
use app\models\Puestos;
use app\models\DetalleEstructuraMovilizacion;
use app\models\Organizaciones;
use app\models\SeguimientoCambios;
use app\helpers\PerfilUsuario;

class SiteController extends Controller
{
    public $defaultAction = 'positiontree';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index', 'positiontree', 'gettree', 'gettreealtern', 'getbranch', 'getresumen', 'getresumennodo', 'getpuestosonmuni', 'getpuestosdepend', 'setpuestopersona', 'getmetabypromotor', 'getmetabyseccion', 'getavancemeta', 'getprogramas', 'getintegrantesprogbyseccion', 'getpuestosfaltantesbyseccion', 'getparents'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'positiontree', 'gettree', 'gettreealtern', 'getbranch', 'getresumen', 'getresumennodo', 'getpuestosonmuni', 'getpuestosdepend', 'setpuestopersona', 'getmetabypromotor', 'getmetabyseccion', 'getavancemeta', 'getprogramas', 'getintegrantesprogbyseccion', 'getpuestosfaltantesbyseccion', 'getparents'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            // change layout for error action
            if ($action->id == 'error')
                $this->layout = 'error';
            return true;
        } else {
            return false;
        }
    }

    public function actionIndex()
    {
        $municipios = MunicipiosUsuario::getMunicipios();

        return $this->render('index', [
            'municipios' => $municipios
        ]);
    }

    public function actionLogin()
    {
        $this->layout = 'blank';

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return Yii::$app->getResponse()->redirect(Url::to(['site/index']));
        } else {
            $login = 'login';

            if (Yii::$app->params['chiapas_unidos']) {
                $login = 'login_chiapas_unidos';
            }

            return $this->render($login, [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        // Elimina todas las coockies y sessionStorage al cerrar sesion
        return $this->render('logout');
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionPositiontree()
    {
        if (!PerfilUsuario::hasPermiso('7cf41ceb-f4a5-4fd8-88c6-3f991348d250', 'R')) {
            return $this->redirect(['site/index']);
        }

        $municipios = MunicipiosUsuario::getMunicipios();
        $puestos = ArrayHelper::map(
            Puestos::find()
                ->select(['IdPuesto', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(), 'IdPuesto', 'Descripcion'
        );

        //DetalleEstructuraMovilizacion::getResumen(102);

        return $this->render('positionTree', [
            'municipios' => $municipios,
            'puestos' => $puestos
        ]);
    }

    public function actionGettree()
    {
        $post = array_filter(Yii::$app->request->post());

        $estructura = new DetalleEstructuraMovilizacion();

        unset($post['_csrf']);
        //$alterna = false;

        if (isset($post['alterna'])) {
            //$alterna = true;
            //unset($post['alterna']);
            $tree = $estructura->getEstrucAlterna($_POST['Municipio'], $_POST['IdPuestoDepende']);
        } else {
            $tree = $estructura->getTree($post);
        }

        return $tree;
    }

    public function actionGettreealtern($idNodo)
    {
        $estructura = new DetalleEstructuraMovilizacion();
        $tree = $estructura->buildTree($idNodo);

        return $tree;
    }


    public function actionGetbranch($idNodo)
    {
        $estructura = new DetalleEstructuraMovilizacion();
        $tree = $estructura->getBranch($idNodo);
        return $tree;
    }

    public function actionGetresumen($idMuni)
    {
        $resumen = DetalleEstructuraMovilizacion::getResumen($idMuni);

        return json_encode($resumen);
    }

    public function actionGetresumennodo($idNodo)
    {
        $resumen = DetalleEstructuraMovilizacion::getResumenNodo($idNodo);
        $tabla = null;

        if(count($resumen) > 0 ) {
            foreach ($resumen as $fila) {
                $tabla[] = $fila;
            }
        }

        return json_encode($tabla);
    }

    public function actionGetpuestosonmuni($idMuni)
    {
        $puestos = DetalleEstructuraMovilizacion::getPuestosOnMuni($idMuni);

        return json_encode($puestos);
    }

    public function actionGetpuestosdepend()
    {
        $result = array();
        $params = array_filter(Yii::$app->request->post());
        unset($params['IdPuesto']);
        unset($params['_csrf']);
        //unset($params['IdPuestoDepende']);

        if( !empty($params) ) {
            $result = DetalleEstructuraMovilizacion::getNodosDependientes($params, true);
        }

        return json_encode($result);
    }

    public function actionSetpuestopersona($claveunica, $nodo)
    {
        $error = false;

        try {
            $persona = DetalleEstructuraMovilizacion::existsPersonaOnEstructura($claveunica);

            if ($persona != false) {
                return json_encode(['error'=>true,
                    'puesto'=>'La persona seleccionada ya esta asignada al puesto '.
                    $persona['puesto'].' ('.$persona['estructura'].')']);
            }

            $nodoEstructura = DetalleEstructuraMovilizacion::findOne($nodo);

            if ($nodoEstructura) {
                $log = new SeguimientoCambios();
                $log->usuario = Yii::$app->user->identity->IdUsuario;
                $log->tabla = 'DetalleEstructuraMovilizacion';
                $log->campo = 'IdPersonaPuesto';
                $log->valor_anterior = $nodoEstructura->IdPersonaPuesto;
                $log->nuevo_valor = $claveunica;
                $log->registro =  (String)$nodoEstructura->IdNodoEstructuraMov;
                $log->accion = SeguimientoCambios::UPDATE;
                $log->fecha = date('Y-m-d H:i:s');

                $nodoEstructura->IdPersonaPuesto = $claveunica;
                $nodoEstructura->save();
                $log->save();
            } else {
                $error = true;
            }
        } catch (Exception $e) {
            $error = true;
        }

        return json_encode(['error'=>$error, 'puesto'=>'']);
    }

    public function actionGetmetabypromotor($id)
    {
        $meta = DetalleEstructuraMovilizacion::getMetaByPromotor($id);

        return $meta;
    }

    public function actionGetmetabyseccion($id, $puesto)
    {
        $meta = 0;
        // Hack para permitir que el 8 COORD. ZONA
        if ($puesto <= 5 || $puesto==8) {
            $meta = DetalleEstructuraMovilizacion::getMetaBySeccion($id);
        } else {
            $meta = DetalleEstructuraMovilizacion::getMetaByPromotor($id);
        }

        return $meta;
    }

    public function actionGetavancemeta($id)
    {
        $meta = DetalleEstructuraMovilizacion::getAvanceMeta($id);

        return $meta;
    }

    public function actionGetprogramas($idMuni, $idNodo, $alterna=false)
    {
        //$programas = Organizaciones::find()->where(['IdMunicipio'=>$idMuni, 'idTipoOrganizacion'=>22])->asArray()->all();
        $programas = Organizaciones::getOrgsOnMuni($idMuni, $alterna);
        /*$nodo = DetalleEstructuraMovilizacion::find()->where('IdNodoEstructuraMov='.$idNodo)->one();
        $seccion = null;
        // Puesto = Jefe de Seccion
        if ($nodo->puesto->Nivel == 5) {
            $seccion = DetalleEstructuraMovilizacion::getSeccionNodo($idNodo);
        }*/

        $secciones = DetalleEstructuraMovilizacion::getSeccionesNodo($idNodo);

        foreach ($programas as $key => $value) {
            $prog = Organizaciones::find()->where(['IdOrganizacion'=>$value['IdOrganizacion']])->one();
            $count = 0;
            if ($prog) {
                //$count = Organizaciones::getCountIntegrantes($value['IdOrganizacion'], $idMuni, $seccion);
                $count = Organizaciones::getCountIntegrantes($value['IdOrganizacion'], $idMuni, $secciones);
            }

            $programas[$key] = array_merge($programas[$key], ['Integrantes'=>$count]);
        }

        return json_encode($programas);
    }

    public function actionGetintegrantesprogbyseccion($idOrg, $idMuni, $idNodo)
    {
        /*$nodo = DetalleEstructuraMovilizacion::find()->where('IdNodoEstructuraMov='.$idNodo)->one();
        $seccion = null;
        // Puesto = Jefe de Seccion
        if ($nodo->puesto->Nivel == 5) {
            $seccion = DetalleEstructuraMovilizacion::getSeccionNodo($idNodo);
        }

        $integrantes = Organizaciones::getCountIntegrantesBySeccion($idOrg, $idMuni, $seccion);*/

        $secciones = DetalleEstructuraMovilizacion::getSeccionesNodo($idNodo);
        $integrantes = Organizaciones::getCountIntegrantesBySeccion($idOrg, $idMuni, $secciones);

        return json_encode($integrantes);
    }

    public function actionGetpuestosfaltantesbyseccion()
    {
        $muni = Yii::$app->getRequest()->post('muni');
        $puesto = Yii::$app->getRequest()->post('puesto');

        $puestos = DetalleEstructuraMovilizacion::getpuestosfaltantesbyseccion($muni, $puesto);

        return json_encode($puestos);
    }

    public function actionGetparents()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $idNodo = Yii::$app->getRequest()->post('nodo');
        $tipo = Yii::$app->getRequest()->post('tipo');

        $padres = DetalleEstructuraMovilizacion::getParents($idNodo, $tipo);

        return $padres;
    }
}
