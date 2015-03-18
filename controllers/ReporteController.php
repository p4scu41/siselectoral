<?php

namespace app\controllers;

use Yii;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use app\models\CMunicipio;
use app\models\Reporte;

class ReporteController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (strtolower(Yii::$app->user->identity->perfil->IdPerfil) == strtolower(Yii::$app->params['idAdmin'])) {
            $listMunicipios = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all();
        } elseif (strtolower(Yii::$app->user->identity->perfil->IdPerfil) == strtolower(Yii::$app->params['idDistrito'])) {
            $listMunicipios = CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->where(['DistritoLocal'=>Yii::$app->user->identity->persona->DISTRITOLOCAL])
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

        $reporteHTML = '';
        $titulo = '';

        if (Yii::$app->request->post('Municipio')) {
            $reporteDatos = Reporte::avanceSeccional( Yii::$app->request->post('Municipio') );
            $omitirCentrado = array(2);
            $reporteHTML = Reporte::arrayToHtml($reporteDatos, $omitirCentrado);
            $municipio = CMunicipio::find()->where(['IdMunicipio' => Yii::$app->request->post('Municipio')])->one();
            $titulo = 'Avance Seccional de '.$municipio->DescMunicipio;
        }

        return $this->render('index',[
            'municipios' => $municipios,
            'reporte' => $reporteHTML,
            'titulo' => $titulo,
        ]);
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
        $pathFile = Yii::getAlias('@runtime').'/tmp/'.strtotime("now").'.csv';

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

        Yii::$app->response->sendContentAsFile($content, $titulo.".xlsx", 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        Yii::$app->end();
    }

}
