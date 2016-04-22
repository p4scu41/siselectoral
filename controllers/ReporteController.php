<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use app\models\CMunicipio;
use app\models\Reporte;
use app\models\DetalleEstructuraMovilizacion;
use app\models\PREPSeccion;
use app\helpers\MunicipiosUsuario;
use app\helpers\PerfilUsuario;

class ReporteController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'generar', 'pdf', 'excel', 'promovidos', 'promovidosduplicados'],
                'rules' => [
                    [
                        'actions' => ['index', 'generar', 'pdf', 'excel', 'promovidos', 'promovidosduplicados'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        if (!PerfilUsuario::hasPermiso('b3d614ee-f96d-4f42-8c36-2a6e4b6eabeb', 'R')) {
            return $this->redirect(['site/index']);
        }

        $municipios = MunicipiosUsuario::getMunicipios();

        return $this->render('index', [
            'municipios' => $municipios
        ]);
    }

    public function actionGenerar()
    {
        $respuesta = [
            'reporteHTML' => '',
            'titulo' => ''
        ];
        $municipio = CMunicipio::find()->where(['IdMunicipio' => Yii::$app->request->post('Municipio')])->one();
        $extra = '';

        if (Yii::$app->request->post('Municipio') || Yii::$app->request->post('tipoEleccion')) {
            if (Yii::$app->request->post('tipoReporte') == 1) { // Avance Seccional
                $titulo = 'Avance de Promoción Seccional ';
                $nameColum = '';
                $valueColum = '';

                switch (Yii::$app->request->post('tipoEleccion')) {
                    case '1': // Presidencia Municipal
                        $nameColum = 'municipio';
                        $valueColum = Yii::$app->request->post('municipio');
                        $municipio = CMunicipio::find()->where(['IdMunicipio' => Yii::$app->request->post('Municipio')])->one();
                        $titulo .= ' de '.$municipio->DescMunicipio.(Yii::$app->request->post('zona') ? ', Zona '.Yii::$app->request->post('zona') : '');
                        break;
                    case '2': // Diputación Local
                        $nameColum = 'distrito_local';
                        $valueColum = Yii::$app->request->post('distritoLocal');
                        $titulo .= ' del Distrito Local '.$valueColum.(Yii::$app->request->post('zona') ? ', Zona '.Yii::$app->request->post('zona') : '');
                        break;
                    case '3': // Diputación Federal
                        $nameColum = 'distrito_federal';
                        $valueColum = Yii::$app->request->post('distritoFederal');
                        $titulo .= ' del Distrito Federal '.$valueColum.(Yii::$app->request->post('zona') ? ', Zona '.Yii::$app->request->post('zona') : '');
                        break;
                    default:
                        return [];
                }
                $respuesta['datos'] = $nameColum.'-'.$valueColum;

                $reporteDatos = Reporte::avanceSeccional($nameColum, $valueColum, Yii::$app->request->post('zona'));
                $omitirCentrado = array(2);
                $respuesta['titulo'] = $titulo;
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

                $nodoEstructura = DetalleEstructuraMovilizacion::findOne(['IdNodoEstructuraMov' => $nodo]);
                $descNodo = $nodoEstructura ? $nodoEstructura->Descripcion : '';
                $respuesta['titulo'] = 'Listado Promotores - Promovidos de '.$descNodo . '('.date('d-m-Y').')';
                $reporteDatos = Reporte::promovidos(Yii::$app->request->post('Municipio'), $nodo, Yii::$app->request->post('tipo_promovido'), Yii::$app->request->post('incluir_domicilio'));
                $totalPromovios = $reporteDatos ? array_shift($reporteDatos) : ['total' => 0];
                $duplicados = Reporte::promovidosDuplicados(Yii::$app->request->post('Municipio'), $nodo);
                $extra = '<div class="text-center">
                            <div class="btn btn-default btnPromovidosDuplicados">'.
                                'Total: Promovidos: '.$totalPromovios['total'].', '.
                                'Duplicados: '.count($duplicados).
                            '</div>'.
                        '</div>';
            }

            $respuesta['reporteHTML'] = $extra.Reporte::arrayToHtml($reporteDatos, $omitirCentrado);
        }

        return json_encode($respuesta);
    }

    public function actionPromovidosduplicados() 
    {
        $respuesta = [
            'reporteHTML' => '',
            'titulo' => 'Promovidos Duplicados'
        ];

        $nodos = Yii::$app->request->post('IdPuestoDepende') ? array_filter(Yii::$app->request->post('IdPuestoDepende')) : [];
        $nodo = null;
        if (count($nodos)) {
            $nodo = array_pop($nodos);
        }

        $duplicados = Reporte::promovidosDuplicados(Yii::$app->request->post('Municipio'), $nodo);
        $respuesta['reporteHTML'] = $extra.Reporte::arrayToHtml($reporteDatos, $omitirCentrado);

        return json_encode($respuesta);
    }

    public function actionPdf()
    {
        $titulo = Yii::$app->request->post('title');
        $content = str_replace('<h3 class="text-center" id="titulo">'.$titulo.'</h3>', '', Yii::$app->request->post('content'));
        $orientation = Pdf::ORIENT_PORTRAIT;
        $colums = (Yii::$app->request->post('columns') ? Yii::$app->request->post('columns') : 1);

        $content = str_replace('<i class="fa fa-check-square-o fa-lg"></i>', 'Si', $content);
        $content = str_replace('<i class="fa fa-square-o fa-lg"></i>', 'No', $content);

        if (strpos($titulo, 'Estructura Municipal') !== false) {
            $orientation = Pdf::ORIENT_LANDSCAPE;
        }

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
                'SetColumns' => [$colums]
            ]
        ]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');
        $headers->add('Set-Cookie', 'fileDownload=true; path=/');
        $headers->add('Cache-Control', 'max-age=60, must-revalidate');

        $pdfApi = $pdf->getApi();
        $pdfApi->SetProtection(['print']);
        //$pdfApi->SetWatermarkText('FV', 0.08);
        //$pdfApi->showWatermarkText = true;

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
        $headers->add('Set-Cookie', 'fileDownload=true; path=/');

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
        if (!PerfilUsuario::hasPermiso('5ea0a4c2-22f6-4fdc-9050-f25a65f3be91', 'R')) {
            return $this->redirect(['site/index']);
        }

        $municipios = MunicipiosUsuario::getMunicipios();

        return $this->render('promovidos', [
            'municipios' => $municipios,
        ]);
    }

    public function actionSeccional()
    {
        if (!PerfilUsuario::hasPermiso('b3d614ee-f96d-4f42-8c36-2a6e4b6eabeb', 'R')) {
            return $this->redirect(['site/index']);
        }

        $municipios = MunicipiosUsuario::getMunicipios();
        $distritosLocales = ArrayHelper::map(
            PREPSeccion::find()->select('distrito_local')->groupBy('distrito_local')->orderBy('distrito_local')->all(),
            'distrito_local', 'distrito_local');
        $distritosFederales = ArrayHelper::map(
            PREPSeccion::find()->select('distrito_federal')->groupBy('distrito_federal')->orderBy('distrito_federal')->all(),
            'distrito_federal', 'distrito_federal');

        return $this->render('seccional', [
            'municipios' => $municipios,
            'distritosLocales' => $distritosLocales,
            'distritosFederales' => $distritosFederales,
        ]);
    }
}
