<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Reporte de Promoción';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Url::to('@web/js/promovidos.js'));
$this->registerJsFile(Url::to('@web/js/plugins/table2CSV.js'));
$this->registerJs('urlPuestos="'.Url::toRoute('site/getpuestosonmuni', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlNodoDepend="'.Url::toRoute('site/getpuestosdepend', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlReporte="'.Url::toRoute('reporte/generar', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlResumen="'.Url::toRoute('site/getresumen', true).'";', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/plugins/json-to-table.js'));
?>

<div class="row">
    <!-- left column -->
    <div class="col-lg-12">

        <!-- general form elements -->
        <div class="box box-primary box-success">

            <div class="panel panel-success" id="panelBuscar">
                <div class="panel-heading">
                    <h3 class="panel-title">Filtros</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                                'options' => ['class' => 'form-inline'],
                                'id' => 'formBuscar',
                            ]); ?>
                        <div id="bodyForm">
                            <div class="form-group">
                                <label for="Municipio">Municipio</label>
                                <?php
                                    //$selectMunicipio = Yii::$app->request->post('Municipio') ? Yii::$app->request->post('Municipio') : Yii::$app->user->identity->persona->MUNICIPIO;
                                ?>
                                <?= Html::dropDownList('Municipio', null, $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'municipio', 'required'=>'true']); ?>
                            </div>
                        </div>
                        <p>
                            <button type="button" class="btn btn-success" id="btnGenerarReporte">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Reporte
                            </button> &nbsp;
                            <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadIndicator"></i>
                        </p>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->

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