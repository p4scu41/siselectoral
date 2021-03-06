<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\helpers\PerfilUsuario;

/* @var $this yii\web\View */
$this->title = 'Estrategia Política';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('urlTree="'.Url::toRoute('site/gettree', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlTreeAltern="'.Url::toRoute('site/gettreealtern', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlBranch="'.Url::toRoute('site/getbranch', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlPerson="'.Url::toRoute('padron/get', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlResumen="'.Url::toRoute('site/getresumen', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlPuestos="'.Url::toRoute('site/getpuestosonmuni', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlNodoDepend="'.Url::toRoute('site/getpuestosdepend', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlResumenNodo="'.Url::toRoute('site/getresumennodo', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('imgNoPerson="'.Url::to('@web/img/avatar/U.jpg', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlBuscarPersona="'.Url::toRoute('padron/buscarajax', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlAsignarPersona="'.Url::toRoute('site/setpuestopersona', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetMetaBySeccion="'.Url::toRoute('site/getmetabyseccion', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetMetaByPromotor="'.Url::toRoute('site/getmetabypromotor', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetAvanceMeta="'.Url::toRoute('site/getavancemeta', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetProgramas="'.Url::toRoute('site/getprogramas', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetIntegrantes="'.Url::toRoute('site/getintegrantesprogbyseccion', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetpuestosfaltantesbyseccion="'.Url::toRoute('site/getpuestosfaltantesbyseccion', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('pl="'.Yii::$app->user->identity->nodoestructura->IdPuesto.'";', \yii\web\View::POS_HEAD);
$this->registerJs('main = 3;', \yii\web\View::POS_HEAD); // ID de Usuario Administrador
//$this->registerJs('logIDUsr = "'.Yii::$app->user->identity->IdUsuario.'";', \yii\web\View::POS_HEAD);
//$this->registerJs('logIDSA = 6;', \yii\web\View::POS_HEAD);
$this->registerJs('logIDPerfUsr = "'.strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil).'";', \yii\web\View::POS_HEAD);
$this->registerJs('IDPerfAdm = "'.strtolower(Yii::$app->params['idAdmin']).'";', \yii\web\View::POS_HEAD);
$this->registerJs('IDPerfAdmMuni = "'.strtolower(Yii::$app->params['idAdminMuni']).'";', \yii\web\View::POS_HEAD);
$this->registerJs('IDPerfCaptu = "'.strtolower(Yii::$app->params['idCapturista']).'";', \yii\web\View::POS_HEAD);
$this->registerJs('IDPerfLecturaZona = "'.strtolower(Yii::$app->params['IDPerfLecturaZona']).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlListInte="'.Url::toRoute('organizacion/listintegrantesfromseccion').'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlUpdatePersona="'.Url::toRoute('padron/update').'";', \yii\web\View::POS_HEAD);
$this->registerJs('getSeccionesMuni="'.Url::toRoute('seccion/getjsmuni', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('getParents="'.Url::toRoute('site/getparents', true).'";', \yii\web\View::POS_HEAD);

// http://stackoverflow.com/questions/14923301/uncaught-typeerror-cannot-read-property-msie-of-undefined-jquery-tools
$this->registerJs('jQuery.browser = {};
(function () {
    jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
})();', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/positionTree.js'));
$this->registerJsFile(Url::to('@web/js/plugins/jquery-scrollto.js'));
$this->registerJsFile(Url::to('@web/js/plugins/jquery.ba-bbq.min.js'));
$this->registerJsFile(Url::to('@web/js/plugins/json-to-table.js'));
$this->registerJsFile(Url::to('@web/js/plugins/jquery.printarea.js'));
$this->registerCssFile(Url::to('@web/css/fancytree/skin-win8-n/ui.fancytree.css'));
?>

<div class="row">
    <!-- left column -->
    <div class="col-lg-12">

        <div id="tablaPrueba"></div>

        <!-- general form elements -->
        <div class="box box-primary box-danger">

            <div class="panel panel-danger" id="panelBuscar">
                <div class="panel-heading">
                    <h3 class="panel-title">Buscar</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                                'options' => ['class' => 'form-inline'],
                                'id' => 'formBuscar',
                                /*'enableClientValidation'=> true,
                                'enableAjaxValidation'=> false,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => true,
                                'action' => Yii::$app->homeUrl . 'your/url/path'*/
                            ]); ?>
                        <div id="bodyForm">
                            <div class="form-group">
                                <label for="Municipio">Municipio</label>
                                <?php
                                    //$selectMunicipio = Yii::$app->user->identity->persona->MUNICIPIO ? Yii::$app->user->identity->persona->MUNICIPIO : Yii::$app->request->post('Municipio');
                                ?>
                                <?= Html::dropDownList('Municipio', null, $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'municipio']); ?>
                            </div>
                            <!--<div class="form-group">
                                <label for="IdPuesto">Puesto</label>
                                <?= Html::dropDownList('IdPuesto', Yii::$app->request->post('IdPuesto'), $puestos, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'puesto']); ?>
                            </div>-->
                            <input type="hidden" name="IdPuesto" id="puesto" value="0">
                        </div>
                        <div id="listJefeSeccion"></div>
                        <p><button type="button" class="btn bg-darkred" id="btnBuscar">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                            </button> &nbsp;
                            <?php if (PerfilUsuario::isAdminGeneral() || PerfilUsuario::isAdminMunicipal()) { ?>
                            <button type="button" class="btn bg-darkred" id="btnResumen" href="#modalResumen" data-toggle="modal" style="display: none;">
                                <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Status
                            </button> &nbsp;
                            <?php } ?>
                            <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadIndicator"></i>
                        </p>
                        
                        <div id="ubicacionSeccion"></div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->

<div class="alert alert-danger" id="alertResult" style="display: none">
    No se encontraron resultados en la b&uacute;squeda
</div>

<div class="panel hidden-lg hidden-md hidden-sm">
    <div class="hidden-lg hidden-md hidden-sm col-xm-12">
        Si no logra ver toda la tabla deslice hacia la derecha <i class="fa fa-arrow-circle-right"></i>
    </div>
</div>

<p class="text-right">
    <button class="btn btn-default btnCollapseTree"><i class="fa fa-compress"></i> </button>
</p>

<div class="table-responsive">
    <table id="treeContainer" class="table table-condensed table-bordered table-hover" style="display: none">
        <colgroup>
            <col width="*"></col>
            <col width="80px"></col>
        </colgroup>
        <thead>
            <tr>
                <th class="text-center">Puesto</th>
                <th class="text-center">Asignaci&oacute;n</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<p class="text-right">
    <button class="btn btn-default btnCollapseTree"><i class="fa fa-compress"></i> </button>
</p>

<div class="modal fade" id="modalPerson" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detalles del puesto</h4>
            </div>
            <div class="modal-body" id="containerPerson">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <img src="" class="img-rounded imgPerson" id="imgPerson">
                        <h5 id="titulo_puesto"></h5>
                    </div>
                    <div class="col-md-9">

                        <div class="row" id="panelControl">
                            <div class="col-sm-12 col-md-12">
                                <div role="tabpanel">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#tabPuesto" aria-controls="tabPuesto" role="tab" data-toggle="tab">Puesto</a></li>
                                        <li role="presentation"><a href="#tabPersona" aria-controls="tabPersona" role="tab" data-toggle="tab">Persona</a></li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="tabPuesto">
                                            <div class="panel panel-danger">
                                                <div class="panel-body">
                                                    <div id="indicadoresPuesto" class="text-center col-xs-12 col-sm-12 col-md-12">
                                                        <span class="btn btn-app btn-sm btn-primary" id="meta_proyec">
                                                            <span class="line-height-1 bigger-170" id="no_meta_proyec"> 0 </span>
                                                            <br>
                                                            <span class=""> Meta Proyec </span>
                                                        </span>
                                                        <span class="btn btn-app btn-sm btn-yellow" id="dependencias">
                                                            <span class="line-height-1 bigger-170" id="no_dependencias"></span>
                                                            <br><span id="descripcion_dependencias"></span>
                                                        </span>
                                                        <span class="btn btn-app btn-sm btn-success" id="meta">
                                                            <span class="line-height-1 bigger-170" id="no_meta"></span>
                                                            <br><span>Meta Est.</span>
                                                        </span>
                                                        <!--<span class="btn btn-app btn-sm btn-primary" id="vacantes">
                                                            <span class="line-height-1 bigger-170" id="no_vacantes"> 4 </span>
                                                            <br>
                                                            <span class=""> Vacantes </span>
                                                        </span>-->
                                                        <span class="btn btn-app btn-sm btn-purple" id="meta_promocion">
                                                            <span class="line-height-1 bigger-170" id="no_meta_promocion">0%</span>
                                                            <br>
                                                            <span class="">Promoción</span>
                                                        </span>
                                                        <span class="btn btn-app btn-sm btn-pink" id="btn_programas">
                                                            <span class="line-height-1 bigger-170" id="no_programas"> 0 </span>
                                                            <br>
                                                            <span class=""> Est. Política </span>
                                                        </span>
                                                        <!--<span class="btn btn-app btn-sm btn-grey">
                                                            <span class="line-height-1 bigger-170"> 0 </span>
                                                            <br>
                                                            <span class=""> Organización </span>
                                                        </span>-->
                                                        <span class="btn btn-app btn-sm btn-info" id="infoEstrucAlterna">
                                                            <span class="line-height-1 bigger-170"> 0 </span>
                                                            <br>
                                                            <span> Est. Alterna </span>
                                                        </span>
                                                    </div><br>

                                                    <div id="seccion_coordinados" style="display: none;" class="col-xs-12 col-sm-12 col-md-12">
                                                        <strong>Coordina a:</strong>
                                                        <div class="row" id="list_coordinados"></div>
                                                    </div>

                                                    <div id="seccion_promocion" style="display: none;" class="col-xs-12 col-sm-12 col-md-12">
                                                    </div>

                                                    <div id="seccion_vacantes" style="display: none;" class="col-xs-12 col-sm-12 col-md-12">
                                                        Puestos inmediatos vacantes:
                                                        <ul id="list_vacantes"></ul>
                                                    </div>

                                                    <div id="seccion_programas" style="display: none;" class="col-xs-12 col-sm-12 col-md-12">
                                                        <span>Programas disponibles en el municipio:</span>
                                                        <div id="total_benefi_progra"></div>
                                                        <div id="list_programas"></div>
                                                        <div id="list_integrantes" class="tblListIntegrantesBySeccion" style="display: none;"></div>
                                                    </div>

                                                    <div id="seccion_resumenNodo" style="display: none;" class="col-xs-12 col-sm-12 col-md-12">
                                                        <strong>AVANCE DE LA ESTRUCTURA DE PROMOCIÓN O ACTIVISMO</strong>
                                                        <div class="panel hidden-lg hidden-md hidden-sm" id="verMasResumenNodo">
                                                            <div class="hidden-lg hidden-md hidden-sm col-xm-12">
                                                                Si no logra ver toda la tabla deslice hacia la derecha <i class="fa fa-arrow-circle-right"></i>
                                                            </div>
                                                        </div>
                                                        <div class="table-responsive" id="resumenNodo">
                                                            <i class="fa fa-refresh fa-spin" style="font-size: x-large;"></i>
                                                        </div>
                                                    </div>

                                                    <div id="divTreeEstrucAlterna" style="display: none;" class="col-xs-12 col-sm-12 col-md-12">
                                                        <div id="total_benefi_alterna"></div>
                                                        <div id="list_alternas"></div>
                                                        <div id="list_integrantes_alternas" class="tblListIntegrantesBySeccion" style="display: none;"></div>
                                                        <!--<div class="">
                                                            <table id="treeEstrucAlterna" class="table table-condensed table-bordered table-hover" style="display: none">
                                                                <colgroup>
                                                                    <col width="*"></col>
                                                                    <col width="80px"></col>
                                                                </colgroup>
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">Puesto</th>
                                                                        <th class="text-center">Asignaci&oacute;n</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>-->
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="tabPersona">
                                            <div class="panel panel-danger">
                                                <div class="panel-body">
                                                    <form class="form-horizontal" method="POST" id="frmPersonDetails"></form>
                                                    <div id="divAsignarPersona"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="fechaResumenNodo" class="pull-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php $form = ActiveForm::begin([
                            'options' => ['class' => 'form-inline'],
                            'method' => 'POST',
                            'action' => Url::toRoute('padron/persona', true)
                        ]); ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> &nbsp;
                <input type="hidden" name="id" id="id" value="">
                <button type="submit" href="" data-url="<?= Url::toRoute('padron/persona', true); ?>" class="btn btn-success" id="btnViewPerson">Ver mas detalles</button>
                <button type="button" class="btn btn-primary" id="printResumenNodo"><i class="fa fa-print"></i> Imprimir</button>
                <?php ActiveForm::end(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modalResumen" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center">Status de la Estructura <span id="tituloResumen"></span></h4>
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
                <button type="button" class="btn btn-success" id="printResumen"><i class="fa fa-print"></i> Imprimir</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modalAsignaPersona" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Asignar persona al puesto</h4>
            </div>
            <div class="modal-body">
                <form class="form-inline" method="POST" id="frmBuscarPersona">
                    <div class="form-group">
                        <label for="MUNICIPIO">Municipio</label>
                        <?php $selectMunicipio = Yii::$app->user->identity->persona->MUNICIPIO ? Yii::$app->user->identity->persona->MUNICIPIO : Yii::$app->request->post('Municipio'); ?>
                        <?= Html::dropDownList('MUNICIPIO', $selectMunicipio, $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'MUNICIPIO_persona']); ?>
                    </div>
                    <div class="form-group">
                        <label for="NOMBRE">Nombre(s)</label>
                        <?= Html::textInput('NOMBRE', NULL, ['class' => 'form-control', 'id' => 'NOMBRE']); ?>
                    </div>
                    <div class="form-group">
                        <label for="APELLIDO_PATERNO">Apellido Paterno</label>
                        <?= Html::textInput('APELLIDO_PATERNO', NULL, ['class' => 'form-control', 'id' => 'APELLIDO_PATERNO']); ?>
                    </div>
                    <div class="form-group">
                        <label for="APELLIDO_MATERNO">Apellido Materno</label>
                        <?= Html::textInput('APELLIDO_MATERNO', NULL, ['class' => 'form-control', 'id' => 'APELLIDO_MATERNO']); ?>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success" id="btnBuscarPersona"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </form>
                <div id="resultBuscarPersona" class="table-responsive">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnSaveAsignaPersona"><i class="fa fa-save"></i> Asignar</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<canvas id="canvas"></canvas>

<div class="modal fade" id="modalListIntegrantes" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Lista de integrantes</h4>
            </div>
            <div class="modal-body">
                Hola mundo
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFaltantesbBySeccion" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Distribución por sección</h4>
            </div>
            <div class="modal-body">
                Hola mundo
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetallesBeneficiario" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Datos del Beneficiario</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>