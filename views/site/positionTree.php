<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Estructura';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('urlTree="'.Url::toRoute('site/gettree', true).'";');
$this->registerJs('urlBranch="'.Url::toRoute('site/getbranch', true).'";');
$this->registerJs('urlPerson="'.Url::toRoute('padron/get', true).'";');
$this->registerJsFile(Url::to('@web/js/positionTree.js'));
$this->registerJsFile(Url::to('@web/js/plugins/jquery-scrollto.js'));
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
                    <!--<form class="form-inline" method="POST" action="#resultSearch">-->
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
                            <div class="form-group">
                                <label for="IdPuesto">Puesto</label>
                                <?= Html::dropDownList('IdPuesto', Yii::$app->request->post('IdPuesto'), $puestos, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'puesto']); ?>
                            </div>
                        </div>
                        <p><a class="btn btn-default" href="#modalAddFilter" data-toggle="modal">
                            <span class="glyphicon glyphicon-check"></span>Agregar Filtro</a></p>
                            <p><button type="button" class="btn btn-success" id="btnBuscar">Buscar</button> &nbsp; 
                                <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadIndicator"></i>
                            </p>
                    <?php ActiveForm::end(); ?>
                    <!--</form>-->
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->

<div class="alert alert-danger" id="alertResult" style="display: none">
    No se encontraron resultados en la b&uacute;squeda
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

<div class="modal fade" id="modalAddFilter">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Agregar Nuevo Filtro</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?= Html::dropDownList('filtros', null, $filtros, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id'=>'filtros']); ?>
                </div>
                <div class="alert alert-danger" id="alertFilter" style="display: none">
                    Debe seleccionar una opci&oacute;n.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnAddFilter">Aceptar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modalPerson">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Persona asignada al puesto</h4>
            </div>
            <div class="modal-body" id="containerPerson">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="" data-path="<?= Url::to('@web/img/avatar/'); ?>" class="img-rounded imgPerson" id="imgPerson">
                        <h3 id="nombreCompleto"></h3>
                    </div>
                    <div class="col-md-8">
                        <div class="row" id="panelControl">
                            <div class="col-sm-12 col-md-12">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Detalles de la persona seleccionada</h3>
                                    </div>
                                    <div class="panel-body">
                                        <form class="form-horizontal" method="POST" id="frmPersonDetails"></form>
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

<div class="modal fade" id="modalNoPerson">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Persona asignada al puesto</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger"><i class="fa fa-frown-o fa-lg"></i> Puesto no asignado</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->