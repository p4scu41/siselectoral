<?php
use yii\helpers\Html;
use yii\helpers\Url;

echo $this->render('_filtros', ['municipios' => $municipios]);

echo '<div id="reporteContainer">';
echo '<h3 class="text-center" id="titulo">'.$titulo.'</h3>';
echo $reporte;
echo '</div>';
$this->registerJsFile(Url::to('@web/js/reporte.js'));
$this->registerJsFile(Url::to('@web/js/plugins/table2CSV.js'));
?>

<?php
if ($reporte != '') {
    ?>
    <a href="#" data-url="<?= Url::to(['reporte/pdf'], true) ?>" class="btn btn-default" id="btnExportPdf">
        <i class="fa fa-file-pdf-o"></i> Exportar a pdf
    </a>

    <a href="#" data-url="<?= Url::to(['reporte/excel'], true) ?>" class="btn btn-default" id="btnExportExcel">
        <i class="fa fa-file-excel-o"></i> Exportar a Excel
    </a>
    <?php
}
?>