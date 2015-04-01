<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Organizaciones */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile(Url::to('@web/js/organizaciones.js'));
$this->registerJs('getSeccionesMuni="'.Url::toRoute('seccion/getbymunicipio', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('buscarPersona="'.Url::toRoute('padron/find', true).'";', \yii\web\View::POS_HEAD);
?>

<div class="organizaciones-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'Nombre')->textInput() ?>

    <?= $form->field($model, 'Siglas')->textInput() ?>

    <?= Html::activeHiddenInput($model, 'IdPersonaRepresentante') ?>
    <?= $form->field($model, 'representante', [
            'inputOptions' => [
                'readonly' => 'true',
                'id' => 'org-representante',
                'value' => $model->representante ?
                    $model->representante->APELLIDO_PATERNO .' '. $model->representante->APELLIDO_MATERNO .' '. $model->representante->NOMBRE : ''
            ]
        ])->textInput() ?>

    <?= Html::activeHiddenInput($model, 'IdPersonaEnlace') ?>
    <?= $form->field($model, 'enlace', [
            'inputOptions' => [
                'readonly' => 'true',
                'id' => 'org-enlace',
                'value' => $model->enlace ?
                    $model->enlace->APELLIDO_PATERNO .' '. $model->enlace->APELLIDO_MATERNO .' '. $model->enlace->NOMBRE : ''
            ]
        ])->textInput() ?>

    <?= $form->field($model, 'IdMunicipio')->dropDownList($municipios, ['prompt' => 'Elija una opción']) ?>

    <?= $form->field($model, 'Observaciones')->textInput() ?>

    <?= $form->field($model, 'idTipoOrganizacion')->dropDownList($tipos, ['prompt' => 'Elija una opción']) ?>

    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', Url::toRoute('organizaciones/index', true), ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div class="modal fade" id="modalBuscarPersona" tabindex="-1" role="dialog" aria-labelledby="modalBuscarPersonaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalBuscarPersonaLabel">Buscar Persona</h4>
            </div>
            <div class="modal-body">
            <?php
                echo Html::beginForm('#', 'POST', [
                        'name' => 'frmBuscarPersona',
                        'id' => 'frmBuscarPersona',
                        'class' => 'form-inline'
                    ]);

                $municipios = [0 => 'Seleccione el municipio'] + $municipios;

                echo '<div class="form-group">';
                echo Html::dropDownList('municipio', null, $municipios, [
                        'class' => 'form-control',
                        'id' => 'municipio',
                    ]);
                echo '</div>';

                echo '<div class="form-group">';
                echo Html::dropDownList('seccion', null, [0=>'Sección'], [
                        'class' => 'form-control',
                        'id' => 'seccion',
                    ]);
                echo '</div>';

                echo '<div class="form-group">';
                echo Html::textInput('apellidoPaterno', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Apellido Paterno'
                    ]);
                echo '</div>';

                echo '<div class="form-group">';
                echo Html::textInput('apellidoMaterno', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Apellido Materno'
                    ]);
                echo '</div>';

                echo '<div class="form-group">';
                echo Html::textInput('nombre', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Nombre'
                    ]);
                echo '</div>';

                echo '<br><div class="text-center">';
                echo Html::button('Buscar', [
                        'class' => 'btn btn-success',
                        'id' => 'btnBuscarPersona'
                    ]);
                echo '</div>';

                echo Html::endForm();
            ?>
                <br>
                <div class="table-responsive" id="resultBuscarPersona">
                    <table id="tblResultBuscarPersona" class="table table-condensed table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Sección</th>
                                <th class="text-center">Casilla</th>
                                <th class="text-center">Dirección</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnAsignarPersona">Aceptar</button>
            </div>
        </div>
    </div>
</div>