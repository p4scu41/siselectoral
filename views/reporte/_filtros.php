<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Reportes';
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
                                    $selectMunicipio = Yii::$app->request->post('Municipio') ? Yii::$app->request->post('Municipio') : Yii::$app->user->identity->persona->MUNICIPIO;
                                ?>
                                <?= Html::dropDownList('Municipio', $selectMunicipio, $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'municipio', 'required'=>'true']); ?>
                            </div>

                            <div class="form-group">
                                <label>Tipo de reporte:</label> &nbsp; &nbsp;
                                <div class="radio">
                                    <label>
                                      <input type="radio" name="tipoReporte" value="1" <?= Yii::$app->request->post('tipoReporte')==1 ? 'checked' : '' ?> >
                                      Avance Seccional
                                    </label>
                                 </div> &nbsp; &nbsp;
                                <div class="radio">
                                    <label>
                                      <input type="radio" name="tipoReporte" value="2" <?= Yii::$app->request->post('tipoReporte')==2 ? 'checked' : '' ?> >
                                      Estructura
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p>
                            <button type="submit" class="btn btn-success" id="btnBuscar">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Generar reporte
                            </button> &nbsp;
                        </p>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->
