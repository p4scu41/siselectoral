<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use app\models\CMunicipio;
use app\models\Reporte;
use app\models\DetalleEstructuraMovilizacion;
use app\helpers\MunicipiosUsuario;

class ReporteController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'generar', 'pdf', 'excel', 'promovidos'],
                'rules' => [
                    [
                        'actions' => ['index', 'generar', 'pdf', 'excel', 'promovidos'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        $municipios = MunicipiosUsuario::getMunicipios();

        return $this->render('index', [
            'municipios' => $municipios,
            //'reporte' => $reporteHTML,
            //'titulo' => $titulo,
        ]);
    }

    public function actionGenerar()
    {
        $respuesta = [
            'reporteHTML' => '',
            'titulo' => ''
        ];

        if (Yii::$app->request->post('Municipio')) {
            $municipio = CMunicipio::find()->where(['IdMunicipio' => Yii::$app->request->post('Municipio')])->one();

            if (Yii::$app->request->post('tipoReporte') == 1) { // Avance Seccional
                $reporteDatos = Reporte::avanceSeccional(Yii::$app->request->post('Municipio'));
                $omitirCentrado = array(2);
                $respuesta['titulo'] = 'Avance Seccional de '.$municipio->DescMunicipio;
            } elseif (Yii::$app->request->post('tipoReporte') == 2) { // Estructura
                $nodos = array_filter(Yii::$app->request->post('IdPuestoDepende'));
                $nodo = null;
                if (count($nodos)) {
                    $nodo = array_pop($nodos);
                }

                $reporteDatos = Reporte::estructura(
                    Yii::$app->request->post('Municipio'),
                    $nodo,
                    Yii::$app->request->post('puestos')
                );
                $omitirCentrado = array(1, 2, 3, 9, 10, 11);
                $respuesta['titulo'] = 'Estructura Municipal de '.$municipio->DescMunicipio;
            } elseif (Yii::$app->request->post('tipoReporte') == 3) { // Promovidos
                $omitirCentrado = array(1, 4);
                $nodos = Yii::$app->request->post('IdPuestoDepende') ? array_filter(Yii::$app->request->post('IdPuestoDepende')) : [];
                $nodo = null;
                if (count($nodos)) {
                    $nodo = array_pop($nodos);
                }

                $respuesta['titulo'] = 'Listado de Promotores con sus respectivos Promovidos';
                $reporteDatos = Reporte::promovidos(Yii::$app->request->post('Municipio'), $nodo, Yii::$app->request->post('tipo_promovido'));
            }

            $respuesta['reporteHTML'] = Reporte::arrayToHtml($reporteDatos, $omitirCentrado);
        }

        return json_encode($respuesta);
    }

    public function actionPdf()
    {
        $content = Yii::$app->request->post('content');
        $titulo = Yii::$app->request->post('title');

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_LETTER ,
            'content' => $content,
            'filename' => $titulo.'.pdf',
            'destination' => Pdf::DEST_DOWNLOAD,
            'options' => [
                'title' => $titulo,
                'subject' => 'SIRECI - Sistema de Red Ciudadana '.date("d-m-Y h:i:s A")
            ],
            'methods' => [
                'SetHeader' => ['|'.$titulo.'|'],
                'SetFooter' => ['SIRECI - Sistema de Red Ciudadana|Pagina {PAGENO}|'.date("d-m-Y h:i:s A")],
            ]
        ]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        return $pdf->render();
    }

    public function actionExcel()
    {
        $content = Yii::$app->request->post('content');
        $titulo = Yii::$app->request->post('title');
        $pathFolder = Yii::getAlias('@runtime').'/tmp';
        $pathFile = $pathFolder.'/'.strtotime("now").'.csv';

        if (!is_dir($pathFolder)) {
            mkdir($pathFolder, 0755);
        }

        file_put_contents($pathFile, $content);

        $objReader = \PHPExcel_IOFactory::createReader('CSV');
        $objReader->setDelimiter(",");
        $objReader->setLineEnding("\n");
        $objReader->setEnclosure('"');
        $objPHPExcel = $objReader->load($pathFile);

        $objPHPExcel->setActiveSheetIndex(0);
        // max length of sheet title
        $objPHPExcel->getActiveSheet()->setTitle(substr($titulo, 0, 30));

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;

        $headers->add('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $headers->add('Content-Disposition', 'attachment; filename="'.$titulo.'.xlsx"');
        $headers->add('Cache-Control', 'max-age=0');
        // If you're serving to IE 9, then the following may be needed
        $headers->add('Cache-Control', 'max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        $headers->add('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        $headers->add('Last-Modified', gmdate('D, d M Y H:i:s').' GMT'); // always modified
        $headers->add('Cache-Control', 'cache, must-revalidate'); // HTTP/1.1
        $headers->add('Pragma', 'public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $content = ob_get_clean();

        Yii::$app->response->sendContentAsFile(
            $content,
            $titulo.".xlsx",
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        Yii::$app->end();
    }

    public function actionPromovidos()
    {
        $municipios = MunicipiosUsuario::getMunicipios();

        return $this->render('promovidos', [
            'municipios' => $municipios,
        ]);
    }
}
