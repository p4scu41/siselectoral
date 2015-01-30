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
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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
        return $this->render('index');
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

    public function actionPositiontree() {
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
        $filtros = [
            'localidad' => 'Localidad',
            'distrito' => 'Distrito',
            'seccion' => 'Sección',
            'organizacion' => 'Organización'
            
        ];
        
        $post = Yii::$app->request->post();
        
        $estructura = new DetalleEstructuraMovilizacion();
        $tree = $estructura->getTree(34259);

        return $this->render('positionTree', [
            'municipios' => $municipios,
            'puestos' => $puestos,
            'filtros' => $filtros,
            'tree' => $tree,
        ]);
    }

}
