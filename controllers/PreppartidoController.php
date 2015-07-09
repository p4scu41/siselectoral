<?php

namespace app\controllers;

use Yii;
use app\models\PREPPartido;
use app\models\PREPPartidoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\helpers\ResizeImage;
use yii\helpers\Url;

/**
 * PREPPartidoController implements the CRUD actions for PREPPartido model.
 */
class PREPPartidoController extends Controller
{
    /**
     * Titulo singular para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_sin = 'Partido';

    /**
     * Titulo plural para breadcrumb y encabezado
     *
     * @var string
     */
    private $titulo_plu = 'Partidos';
    
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
     * Lists all PREPPartido models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PREPPartidoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
        ]);
    }

    /**
     * Displays a single PREPPartido model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $pathLogo = Url::to('@app/partidos', true).'/'.$model->id_partido.'.jpg';
        $logoPartido = null;

        if ( file_exists($pathLogo) ) {
            $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
            $imageByte = file_get_contents($pathLogo);
            $logoPartido = 'data:image/' . $type . ';base64,' . base64_encode($imageByte);
        }

        return $this->render('view', [
            'model' => $model,
            'titulo_sin' => $this->titulo_sin,
            'titulo_plu' => $this->titulo_plu,
            'logoPartido' => $logoPartido
        ]);
    }

    /**
     * Creates a new PREPPartido model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PREPPartido();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id_partido]);
            $pathLogo = Url::to('@app/partidos', true).'/'.$model->id_partido.'.jpg';
            $logo = UploadedFile::getInstanceByName('logo');

            if ($logo != null) {
                $logo->saveAs($pathLogo);
                list($ancho, $alto) = getimagesize($pathLogo);

                if ($ancho > 200 || $alto > 250) {
                     // Redimensionar
                    ResizeImage::smart_resize_image($pathLogo, null, 200, 250, false , $pathLogo, false, false, 100);
                }
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
            ]);
        }
    }

    /**
     * Updates an existing PREPPartido model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $pathLogo = Url::to('@app/partidos', true).'/'.$model->id_partido.'.jpg';
        $logoPartido = null;

        if ( file_exists($pathLogo) ) {
            $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
            $imageByte = file_get_contents($pathLogo);
            $logoPartido = 'data:image/' . $type . ';base64,' . base64_encode($imageByte);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id_partido]);
            $pathLogo = Url::to('@app/partidos', true).'/'.$model->id_partido.'.jpg';
            $logo = UploadedFile::getInstanceByName('logo');

            if ($logo != null) {
                $logo->saveAs($pathLogo);
                list($ancho, $alto) = getimagesize($pathLogo);

                if ($ancho > 200 || $alto > 250) {
                     // Redimensionar
                    ResizeImage::smart_resize_image($pathLogo, null, 200, 250, false , $pathLogo, false, false, 100);
                }
            }
            
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'titulo_sin' => $this->titulo_sin,
                'titulo_plu' => $this->titulo_plu,
                'logoPartido' => $logoPartido
            ]);
        }
    }

    /**
     * Deletes an existing PREPPartido model.
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
     * Finds the PREPPartido model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PREPPartido the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PREPPartido::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('La página solicitada no existe.');
        }
    }
}
