<?php
use yii\helpers\Url;

$this->registerJsFile(Url::to('@web/js/selectPuestos.js'));
$this->registerJsFile(Url::to('@web/js/reporte.js'));
$this->registerJsFile(Url::to('@web/js/plugins/table2CSV.js'));
$this->registerJsFile(Url::to('@web/js/plugins/moment.min.js'));
$this->registerJs('urlPuestos="'.Url::toRoute('site/getpuestosonmuni', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlNodoDepend="'.Url::toRoute('site/getpuestosdepend', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlReporte="'.Url::toRoute('reporte/generar', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlResumen="'.Url::toRoute('site/getresumen', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetAuditoria="'.Url::toRoute('auditoriaestructura/getauditoria', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlSetAuditoria="'.Url::toRoute('auditoriaestructura/setauditoria', true).'";', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/plugins/json-to-table.js'));

echo $this->render('_filtros', ['municipios' => $municipios]);
?>

<div class="opcionesExportar" style="display: none;">
    <a href="#" data-url="<?= Url::to(['reporte/pdf'], true) ?>" class="btn btn-default btnExportPdf">
        <i class="fa fa-file-pdf-o"></i> Exportar a pdf
    </a>
    <!--<a href="#" data-url="<?= Url::to(['reporte/excel'], true) ?>" class="btn btn-default btnExportExcel">
        <i class="fa fa-file-excel-o"></i> Exportar a Excel
    </a>-->
</div>

<div class="alert alert-danger" id="alertResult" style="display: none">
    No se encontraron resultados en la b&uacute;squeda
</div>

<?PHP
echo '<div id="reporteContainer">';
echo '<h3 class="text-center" id="titulo"></h3>';
echo '<div id="tabla_reporte"></div>';
echo '</div>';
?>

<div class="opcionesExportar" style="display: none;">
    <a href="#" data-url="<?= Url::to(['reporte/pdf'], true) ?>" class="btn btn-default btnExportPdf">
        <i class="fa fa-file-pdf-o"></i> Exportar a pdf
    </a>
    <!--<a href="#" data-url="<?= Url::to(['reporte/excel'], true) ?>" class="btn btn-default btnExportExcel">
        <i class="fa fa-file-excel-o"></i> Exportar a Excel
    </a>-->
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

<div id="formExport" style="display:none"></div>


<div class="modal fade" id="modalAuditar" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Auditar Estructura</h4>
            </div>
            <div class="modal-body">
                <form name="formAuditoria" id="formAuditoria">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                    <input type="hidden" name="IdNodoEstructuraMov" value="">
                    <table class="table table-condensed table-bordered table-hover" border="1" cellpadding="1" cellspacing="1">
                        <thead>
                            <tr>
                                <th>Puesto</th>
                                <th id="label_puesto"></th>
                                <th class="text-center">Auditar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Descripción</td>
                                <td id="label_descripcion"></td>
                                <td class="text-center"><label style="display: block;"><input type="checkbox" name="Puesto" value="1" ></label></td>
                            </tr>
                            <tr>
                                <td>Nombre</td>
                                <td id="label_nombre"></td>
                                <td class="text-center"><label style="display: block;"><input type="checkbox" name="Persona" value="1" ></label></td>
                            </tr>
                            <tr>
                                <td>Sección</td>
                                <td id="label_seccion"></td>
                                <td class="text-center"><label style="display: block;"><input type="checkbox" name="Seccion" value="1" ></label></td>
                            </tr>
                            <tr>
                                <td>Número de Celular</td>
                                <td id="label_celular"></td>
                                <td class="text-center"><label style="display: block;"><input type="checkbox" name="Celular" value="1" ></label></td>
                            </tr>
                            <tr>
                                <td>Fecha de Revisión</td>
                                <td id="label_fecha" colspan="2"></td>
                            </tr>
                            <tr>
                                <td>Observaciones</td>
                                <td colspan="2"><textarea name="Observaciones" class="form-control"></textarea></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->