<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Reporte de Estructura Municipal';
$this->params['breadcrumbs'][] = $this->title;
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
                            <div class="form-group no-delete">
                                <button type="button" class="btn btn-success" id="btnResumen" href="#modalResumen" data-toggle="modal">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Resumen
                                </button> &nbsp;
                                <!--<button type="button" class="btn btn-success" id="btnReporteSeccional">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Avance Seccional
                                </button>-->
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
