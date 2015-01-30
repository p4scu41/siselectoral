<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Estructura';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('urlTree="'.Url::toRoute('site/gettree', true).'";');
$this->registerJs('urlBranch="'.Url::toRoute('site/getbranch', true).'";');
$this->registerJsFile(Url::to('@web/js/positionTree.js'));
//$this->registerJsFile(Url::to('@web/js/plugins/fancytree/jquery.fancytree.js', ['position' => \yii\web\View::POS_READY]));
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
                                <label for="municipio">Municipio</label>
                                <?= Html::dropDownList('municipio', Yii::$app->request->post('municipio'), $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control']); ?>
                            </div>
                            <div class="form-group">
                                <label for="puesto">Puesto</label>
                                <?= Html::dropDownList('puesto', Yii::$app->request->post('puesto'), $puestos, ['prompt' => 'Elija una opción', 'class' => 'form-control']); ?>
                            </div>
                        </div>
                        <p><a class="btn btn-default" href="#modalAddFilter" data-toggle="modal">
                            <span class="glyphicon glyphicon-check"></span>Agregar Filtro</a></p>
                        <p><button type="button" class="btn btn-success" id="btnBuscar">Buscar</button></p>
                    <?php ActiveForm::end(); ?>
                    <!--</form>-->
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->

<div id="treeContainer"></div>

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
                    <i class="fa fa-remove"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
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