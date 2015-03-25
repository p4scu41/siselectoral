<?php
use yii\helpers\Url;

$this->registerJsFile(Url::to('@web/js/reporte.js'));
$this->registerJsFile(Url::to('@web/js/plugins/table2CSV.js'));
$this->registerJs('urlPuestos="'.Url::toRoute('site/getpuestosonmuni', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlNodoDepend="'.Url::toRoute('site/getpuestosdepend', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlReporte="'.Url::toRoute('reporte/generar', true).'";', \yii\web\View::POS_HEAD);

echo $this->render('_filtros', ['municipios' => $municipios]);

echo '<div id="reporteContainer">';
echo '<h3 class="text-center" id="titulo">'.$titulo.'</h3>';
echo '<div id="tabla_reporte">'.$reporte.'</div>';
echo '</div>';

?>
<div id="opcionesExportar" style="display: none;">
    <a href="#" data-url="<?= Url::to(['reporte/pdf'], true) ?>" class="btn btn-default" id="btnExportPdf">
        <i class="fa fa-file-pdf-o"></i> Exportar a pdf
    </a>
    <a href="#" data-url="<?= Url::to(['reporte/excel'], true) ?>" class="btn btn-default" id="btnExportExcel">
        <i class="fa fa-file-excel-o"></i> Exportar a Excel
    </a>
</div>