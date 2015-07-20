<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile(Url::to('@web/js/bingo.js'));
$this->registerJsFile(Url::to('@web/js/plugins/jquery.fileDownload.js'));
$this->registerJsFile(Url::to('@web/js/plugins/jquery.printarea.js'));
$this->registerJsFile(Url::to('@web/js/plugins/table2CSV.js'));
$this->registerCssFile(Url::to('@web/css/bingo.css'),  ['depends' => [\yii\bootstrap\BootstrapAsset::className()]]);
$this->registerJs('getSeccionesMuni="'.Url::toRoute('seccion/getjsmuni', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('getPromotoresBySeccion="'.Url::toRoute('seccion/getpromotores', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('getPromovidosByPromotor="'.Url::toRoute('bingo/getpromovidos', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('getPromovidosBySeccion="'.Url::toRoute('bingo/getpromovidosseccion', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('getRCFromSeccion="'.Url::toRoute('prepcasillaseccion/getfromseccion', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('setParticipacion="'.Url::toRoute('bingo/setparticipacion', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('getAvance="'.Url::toRoute('bingo/getavance', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('getInfoPromo="'.Url::toRoute('bingo/getinfopromotor', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlReportepdf="'.Url::toRoute('reporte/pdf', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlStatussecciones="'.Url::toRoute('bingo/statussecciones', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlReporteExcel="'.Url::toRoute(['reporte/excel'], true).'";', \yii\web\View::POS_HEAD);


/* @var $this yii\web\View */
$this->title = 'Bingo';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <!-- left column -->
    <div class="col-lg-12">

        <!-- general form elements -->
        <div class="box box-primary box-success">

            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Buscar</h3>
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
                                    //$selectMunicipio = Yii::$app->user->identity->persona->MUNICIPIO ? Yii::$app->user->identity->persona->MUNICIPIO : Yii::$app->request->post('Municipio');
                                ?>
                                <?= Html::dropDownList('Municipio', null, $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'municipio']); ?>
                            </div>
                            <div class="form-group" id="listJefeSeccion"></div>
                        </div>
                        <div id="alertResult"></div>
                        <p><button type="button" class="btn btn-success" id="btnBuscar">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                            </button> &nbsp;
                            <button type="button" class="btn btn-success" id="btnGenerarListado">
                                <i class="fa fa-refresh fa-list-ol"></i> Generar Listado
                            </button> &nbsp; 
                            <button type="button" class="btn btn-success" id="btnGenerarBingo">
                                <i class="fa fa-building-o"></i> Generar Bingo
                            </button> &nbsp; 
                            <button type="button" class="btn btn-success" id="btnVerRCs">
                                <i class="fa fa-building-o"></i> Ver RCs
                            </button> &nbsp; 
                            <button type="button" class="btn btn-success" id="btnStatusSecciones">
                                <i class="fa fa-building-o"></i> Status Secciones
                            </button>
                            <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadIndicator"></i>
                        </p>

                        <div id="resumen_promocion"></div>
                        
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->

<div class="row">
    <div class="col-md-5">
        <div class="box box-primary box-success">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Promotores</h3>
                </div>
                <div class="panel-body" id="bingoListPromotores">
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="box box-primary box-success">

        <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Información del Promotor</h3>
                </div>
                <div class="panel-body">
                    <p>
                        <!--<button type="button" class="btn btn-success btn-xs" id="btnSeleccionarTodos">Seleccionar todos</button>
                        <button type="button" class="btn btn-success btn-xs" id="btnParticipar">Participar</button>-->
                        <button type="button" class="btn btn-success btn-xs" id="btnVerFaltantes">Ver Promovidos en espera</button>
                    </p>
                    <!--<div id="bingoListPromovidos"></div>-->
                    <div id="infoPromotor"></div>

                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal modal-wide fade" id="modalBingo" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Bingo</h4>
            </div>
            <div class="modal-body">

                <div class="bs-glyphicons">
                    <ul class="bs-glyphicons-list" id="itemsBingo">
                    </ul>
                </div>

            </div>
            <div class="modal-footer">
                <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadSetBingo"></i>
                <button type="button" class="btn btn-success" id="btnAceptarBingo">Aceptar</button>
                <button type="button" class="btn btn-info" id="btnImprimirBingo">Imprimir</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-wide fade" id="modalListado" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Listado</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-success" id="btnImprimirListado">Imprimir</button>-->
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-wide fade" id="modalStatusSecciones" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Status de las Secciones</h4>
            </div>
            <div class="modal-body">
                <div class="bs-glyphicons">
                    <ul class="bs-glyphicons-list" id="listStatusSecciones">
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnImprimirStatusSecciones">Imprimir</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<form action="<?= Url::to(['reporte/excel'], true) ?>" method="POST" class="hidden" id="frmSendStatusSecciones" target="_blank">
    <input type="text" name="title" id="title" value="Status de las Secciones">
    <textarea name="content" id="content"></textarea>
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
</form>