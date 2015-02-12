<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Estrategia Municipal';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('urlTree="'.Url::toRoute('site/gettree', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlBranch="'.Url::toRoute('site/getbranch', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlPerson="'.Url::toRoute('padron/get', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlResumen="'.Url::toRoute('site/getresumen', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlPuestos="'.Url::toRoute('site/getpuestosonmuni', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlNodoDepend="'.Url::toRoute('site/getpuestosdepend', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlResumenNodo="'.Url::toRoute('site/getresumennodo', true).'";', \yii\web\View::POS_HEAD);
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
        <!-- general form elements -->
        <div class="box box-primary box-success">

            <div class="panel panel-success" id="panelBuscar">
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
                                <?= Html::dropDownList('Municipio', Yii::$app->request->post('Municipio'), $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'municipio']); ?>
                            </div>
                            <!--<div class="form-group">
                                <label for="IdPuesto">Puesto</label>
                                <?= Html::dropDownList('IdPuesto', Yii::$app->request->post('IdPuesto'), $puestos, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'puesto']); ?>
                            </div>-->
                            <input type="hidden" name="IdPuesto" id="puesto" value="0">
                        </div>
                        <p><button type="button" class="btn btn-success" id="btnBuscar">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                            </button> &nbsp;
                            <button type="button" class="btn btn-success" id="btnResumen" href="#modalResumen" data-toggle="modal" style="display: none;">
                                <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Status
                            </button> &nbsp;
                            <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadIndicator"></i>
                        </p>
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

<div class="table-responsive">
    <table id="treeContainer" class="table table-condensed table-striped table-bordered table-hover" style="display: none">
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
                                            <div class="panel panel-success">
                                                <div class="panel-body">
                                                    <div id="seccion_coordinados">
                                                        Coordina a <span id="no_coordinados"></span> <span id="nombre_coordinados"></span> :
                                                        <ul id="list_coordinados"></ul>
                                                    </div>

                                                    <div id="seccion_vacantes">
                                                        Puestos inmediatos vacantes (<span id="no_vacantes"></span>):
                                                        <ul id="list_vacantes"></ul>
                                                    </div>

                                                    Status de la Estructura dependiente del puesto seleccionado:
                                                    <div class="panel hidden-lg hidden-md hidden-sm">
                                                        <div class="hidden-lg hidden-md hidden-sm col-xm-12">
                                                            Si no logra ver toda la tabla deslice hacia la derecha <i class="fa fa-arrow-circle-right"></i>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive" id="resumenNodo">
                                                        <i class="fa fa-refresh fa-spin" style="font-size: x-large;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="tabPersona">
                                            <div class="panel panel-success">
                                                <div class="panel-body">
                                                    <form class="form-horizontal" method="POST" id="frmPersonDetails"></form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <a href="" data-url="<?= Url::toRoute('padron/persona', true); ?>" class="btn btn-success" id="btnViewPerson">Ver mas detalles</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
                <button type="button" class="btn btn-success" id="printResumen"><i class="fa fa-print"></i> Imprimir</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->