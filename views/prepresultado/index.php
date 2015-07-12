<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Resultados';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(Url::to('@web/css/resultado.css'));

$this->registerJsFile(Url::to('@web/js/prepresultado.js'));
$this->registerJsFile(Url::to('@web/js/plugins/zingchart/zingchart.min.js'), ['position' => yii\web\View::POS_BEGIN]);
$this->registerJs('zingchart.MODULESDIR = "'.Url::to('@web/js/plugins/zingchart/modules').'"', yii\web\View::POS_BEGIN);
$this->registerJs('urlGetCandidatos = "'.Url::toRoute('prepcandidato/get').'"', yii\web\View::POS_HEAD);
$this->registerJs('urlGetSecciones = "'.Url::toRoute('prepseccion/getbyattr').'"', yii\web\View::POS_HEAD);
$this->registerJs('urlGetZonas = "'.Url::toRoute('prepseccion/getzonas').'"', yii\web\View::POS_HEAD);
$this->registerJs('urlGetResultados = "'.Url::toRoute('prepresultado/get').'"', yii\web\View::POS_HEAD);
?>

<div class="row">
    <!-- left column -->
    <div class="col-lg-12">

        <!-- general form elements -->
        <div class="box box-primary box-success">

            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Elección</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                                'options' => ['class' => 'form-inline'],
                                'id' => 'formFiltroVotos',
                            ]); ?>
                        <div id="bodyForm">
                            <div class="form-group">
                                <label for="tipoEleccion">Tipo de Elección: </label>
                                <?= Html::dropDownList('tipoEleccion', Yii::$app->request->post('tipoEleccion'), $tiposEleccion, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'tipoEleccion']); ?>
                            </div>
                            <div class="form-group <?= Yii::$app->request->post('municipio')!=null ? '' : 'hidden' ?>">
                                <label for="municipio">Municipio: </label>
                                <?= Html::dropDownList('municipio', Yii::$app->request->post('municipio'), $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'municipio']); ?>
                            </div>
                            <div class="form-group <?= Yii::$app->request->post('distritoLocal')!=null ? '' : 'hidden' ?>">
                                <label for="distritoLocal">Distrito Local: </label>
                                <?= Html::dropDownList('distritoLocal', Yii::$app->request->post('distritoLocal'), $distritosLocales, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'distritoLocal']); ?>
                            </div>
                            <div class="form-group <?= Yii::$app->request->post('distritoFederal')!=null ? '' : 'hidden' ?>">
                                <label for="distritoFederal">Distrito Federal: </label>
                                <?= Html::dropDownList('distritoFederal', Yii::$app->request->post('distritoFederal'), $distritosFederales, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'distritoFederal']); ?>
                            </div>
                            <div class="form-group <?= Yii::$app->request->post('tipoEleccion')!=null ? '' : 'hidden' ?>" id="div_zonas">
                                <label>Zonas: </label>
                                <?= Html::dropDownList('zona', Yii::$app->request->post('zona'), $zonas, ['prompt' => 'Inicio', 'class' => 'form-control', 'id' => 'zona']); ?>
                            </div>
                            <div class="form-group <?= Yii::$app->request->post('iniSeccion')!=null || Yii::$app->request->post('zona')!=null? '' : 'hidden' ?>" id="div_secciones">
                                <label>Secciones: </label>
                                <?= Html::dropDownList('iniSeccion', Yii::$app->request->post('iniSeccion'), $secciones, ['prompt' => 'Inicio', 'class' => 'form-control', 'id' => 'iniSeccion']); ?>
                                <?= Html::dropDownList('finSeccion', Yii::$app->request->post('finSeccion'), $secciones, ['prompt' => 'Fin', 'class' => 'form-control', 'id' => 'finSeccion']); ?>
                            </div>
                        </div>
                        <div id="alertResult"></div>
                        <p>
                            <button type="button" class="btn btn-success" id="btnAceptar">
                                <i class="fa fa-building-o"></i> Aceptar
                            </button>
                            <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadIndicator"></i>
                        </p>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->

</div>   <!-- /.row -->

<div id="tabs_resultado" class="hidden">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#tab_grafica_resultado" aria-controls="tab_grafica_resultado" role="tab" data-toggle="tab">Gráfica</a></li>
        <li role="presentation"><a href="#tab_tabla_resultado" aria-controls="tab_tabla_resultado" role="tab" data-toggle="tab">Tabla</a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab_grafica_resultado">
            <div id="grafica_resultado"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="tab_tabla_resultado">
            <div id="tabla_resultado"></div>
        </div>
    </div>
</div>