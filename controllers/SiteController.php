<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\CMunicipio;
use app\models\Puestos;
use app\models\DetalleEstructuraMovilizacion;

class SiteController extends Controller
 {
    public $defaultAction = 'positiontree';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index', 'positiontree', 'gettree', 'gettreealtern', 'getbranch', 'getresumen', 'getresumennodo', 'getpuestosonmuni', 'getpuestosdepend', 'setpuestopersona', 'getmetabypromotor', 'getmetabyseccion', 'getavancemeta'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'positiontree', 'gettree', 'gettreealtern', 'getbranch', 'getresumen', 'getresumennodo', 'getpuestosonmuni', 'getpuestosdepend', 'setpuestopersona', 'getmetabypromotor', 'getmetabyseccion', 'getavancemeta'],
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

    public function actionIndex()
    {
        $municipios = ArrayHelper::map(
            CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all(), 'IdMunicipio', 'DescMunicipio'
        );

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
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
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
        $municipios = ArrayHelper::map(
            CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all(), 'IdMunicipio', 'DescMunicipio'
        );
        $puestos = ArrayHelper::map(
            Puestos::find()
                ->select(['IdPuesto', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(), 'IdPuesto', 'Descripcion'
        );

        DetalleEstructuraMovilizacion::getResumen(102);

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
        $alterna = false;

        if (isset($post['alterna'])) {
            $alterna = true;
            unset($post['alterna']);
        }

        $tree = $estructura->getTree($post, $alterna);

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
            $result = DetalleEstructuraMovilizacion::getNodosDependientes($params);
        }

        return json_encode($result);
    }

    public function actionSetpuestopersona($claveunica, $nodo)
    {
        $error = false;

        try {
            $nodoEstructura = DetalleEstructuraMovilizacion::findOne($nodo);

            if ($nodoEstructura) {
                $nodoEstructura->IdPersonaPuesto = $claveunica;
                $nodoEstructura->save();
            } else {
                $error = true;
            }
        } catch (Exception $e) {
            $error = true;
        }

        return json_encode(['error'=>$error]);
    }

    public function actionGetmetabypromotor($id)
    {
        $meta = DetalleEstructuraMovilizacion::getMetaByPromotor($id);

        return $meta;
    }

    public function actionGetmetabyseccion($id, $puesto)
    {
        $meta = 0;
        if ($puesto <= 5) {
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
}
