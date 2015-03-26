<?php
use yii\helpers\Url;

$this->registerJsFile(Url::to('@web/js/reporte.js'));
$this->registerJsFile(Url::to('@web/js/plugins/table2CSV.js'));
$this->registerJs('urlPuestos="'.Url::toRoute('site/getpuestosonmuni', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlNodoDepend="'.Url::toRoute('site/getpuestosdepend', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlReporte="'.Url::toRoute('reporte/generar', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlResumen="'.Url::toRoute('site/getresumen', true).'";', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/plugins/json-to-table.js'));

echo $this->render('_filtros', ['municipios' => $municipios]);
?>

<div class="opcionesExportar" style="display: none;">
    <a href="#" data-url="<?= Url::to(['reporte/pdf'], true) ?>" class="btn btn-default btnExportPdf">
        <i class="fa fa-file-pdf-o"></i> Exportar a pdf
    </a>
    <a href="#" data-url="<?= Url::to(['reporte/excel'], true) ?>" class="btn btn-default btnExportExcel">
        <i class="fa fa-file-excel-o"></i> Exportar a Excel
    </a>
</div>

<div class="alert alert-danger" id="alertResult" style="display: none">
    No se encontraron resultados en la b&uacute;squeda
</div>

<?PHP
echo '<div id="reporteContainer">';
echo '<h3 class="text-center" id="titulo">'.$titulo.'</h3>';
echo '<div id="tabla_reporte">'.$reporte.'</div>';
echo '</div>';
?>

<div class="opcionesExportar" style="display: none;">
    <a href="#" data-url="<?= Url::to(['reporte/pdf'], true) ?>" class="btn btn-default btnExportPdf">
        <i class="fa fa-file-pdf-o"></i> Exportar a pdf
    </a>
    <a href="#" data-url="<?= Url::to(['reporte/excel'], true) ?>" class="btn btn-default btnExportExcel">
        <i class="fa fa-file-excel-o"></i> Exportar a Excel
    </a>
</div>

<div class="modal fade" id="modalResumen" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Status de la Estructura <span id="tituloResumen"></span></h4>
            </div>
            <div class="modal-body">
                <div class="panel hidden-lg hidden-md hidden-sm">
                    <div class="hidden-lg hidden-md hidden-sm col-xm-12">
                        Si no logra ver toda la tabla deslice hacia la derecha <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </div>
                <div class="table-responsive"></div>
                <div id="fechaResumen" class="pull-right"></div><br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="formExport"></div>