<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\PadronGlobal */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs('$("input:not([type=file])").change(function() {
		$(this).val( normalize($(this).val()).toUpperCase() );
	});');
?>

<div class="padron-global-form">
    <div class="box box-primary box-success">
        <div class="panel panel-success" id="containerPerson">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-11">
                    <?php $form = ActiveForm::begin([
                                'layout' => 'horizontal',
                                'options' => ['enctype' => 'multipart/form-data']
                                //'options' => ['class' => 'form-horizontal'],
                                //'fieldConfig' => ['template' => "<div class=\"column-2\">{label}</div>\n<div class=\"column-5\">{input}</div>"]
                            ]); ?>

                    <div class="text-center">
                        <img src="<?= $model->getFoto() ?>" class="img-rounded imgPerson" id="imgPerson">
                    </div> <br>

                    <?= $form->errorSummary($model); ?>

                    <?= $form->field($model, 'foto')->fileInput(['class'=>'file',
                            'data-browse-label'=> 'Seleccionar Foto',
                            'data-browse-icon'=>'<i class="glyphicon glyphicon-picture"></i> &nbsp;',
                            'data-remove-label'=> "Eliminar",
                            'data-allowed-file-extensions'=> '["jpg", "jpeg", "png", "gif"]',
                            'data-show-upload'=> 'false',
                            'data-msg-invalid-file-extension'=> 'Extensión no permitida en el archivo seleccionado "{name}". Solo se permiten las extensiones "{extensions}"',
                        ]) ?>

                    <?php $disabled = $model->APELLIDO_PATERNO ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'APELLIDO_PATERNO', [
                            //'template' => "{label}\n<i class='fa fa-user'></i>\n{input}\n{hint}\n{error}"
                            //'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
                        ])->textInput($disabled) ?>

                    <?php $disabled = $model->APELLIDO_MATERNO ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'APELLIDO_MATERNO')->textInput($disabled) ?>

                    <?php $disabled = $model->NOMBRE ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'NOMBRE')->textInput($disabled) ?>

                    <?php $disabled = $model->FECHANACIMIENTO ? ['class'=>'form-control', 'disabled'=>'true', 'required'=>'true'] : ['class'=>'form-control', 'required'=>'true'] ?>
                    <?= $form->field($model, 'FECHANACIMIENTO')->widget(DateControl::classname(), [
                            'displayFormat' => 'dd/MM/yyyy',
                            'saveFormat' => 'yyyy-MM-dd',
                            'autoWidget' => false,
                            'widgetClass' => 'yii\widgets\MaskedInput',
                            'options' => [
                                'mask' => '99/99/9999',
                                'options' => $disabled,
                            ],
                        ]);
                    ?>

                    <?php $disabled = $model->SEXO ? ['prompt' => 'Elija una opción', 'disabled'=>'true'] : ['prompt' => 'Elija una opción'] ?>
                    <?= $form->field($model, 'SEXO')->dropDownList(['M'=>'Mujer', 'H'=>'Hombre'], $disabled) ?>

                    <?php $disabled = $model->ESTADO_CIVIL ? ['prompt' => 'Elija una opción', 'disabled'=>'true'] : ['prompt' => 'Elija una opción'] ?>
                    <?= $form->field($model, 'ESTADO_CIVIL')->dropDownList($estado_civil, $disabled) ?>

                    <?php $disabled = $model->OCUPACION ? ['prompt' => 'Elija una opción', 'disabled'=>'true'] : ['prompt' => 'Elija una opción'] ?>
                    <?= $form->field($model, 'OCUPACION')->dropDownList($ocupacion, $disabled) ?>

                    <?php $disabled = $model->ESCOLARIDAD ? ['prompt' => 'Elija una opción', 'disabled'=>'true'] : ['prompt' => 'Elija una opción'] ?>
                    <?= $form->field($model, 'ESCOLARIDAD')->dropDownList($escolaridad, $disabled) ?>

                    <?php $disabled = $model->MUNICIPIO ? ['prompt' => 'Elija una opción', 'disabled'=>'true'] : ['prompt' => 'Elija una opción'] ?>
                    <?= $form->field($model, 'MUNICIPIO')->dropDownList($municipios, $disabled) ?>

                    <?php $disabled = $model->DOMICILIO ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'DOMICILIO')->textInput($disabled) ?>

                    <?php $disabled = $model->NUM_INTERIOR ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'NUM_INTERIOR')->textInput($disabled) ?>

                    <?php $disabled = $model->NUM_EXTERIOR ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'NUM_EXTERIOR')->textInput($disabled) ?>

                    <?php $disabled = $model->MANZANA ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'MANZANA')->textInput($disabled) ?>

                    <?php $disabled = $model->CODIGO_POSTAL ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'CODIGO_POSTAL')->textInput($disabled) ?>

                    <?php $disabled = $model->COLONIA ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'COLONIA')->textInput($disabled) ?>

                    <?php $disabled = $model->DES_LOC ? ['disabled'=>'true', 'placeholder'=>'Ejem. Col., Barr., Ejido, Rancheria.'] : ['placeholder'=>'Ejem. Col., Barr., Ejido, Rancheria.'] ?>
                    <?= $form->field($model, 'DES_LOC')->textInput($disabled) ?>

                    <?php $disabled = $model->NOM_LOC ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'NOM_LOC')->textInput($disabled) ?>

                    <?php $disabled = $model->CORREOELECTRONICO ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'CORREOELECTRONICO')->textInput($disabled) ?>

                    <?php $disabled = $model->TELMOVIL ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'TELMOVIL')->textInput($disabled) ?>

                    <?php $disabled = $model->TELCASA ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'TELCASA')->textInput($disabled) ?>

                    <?php $disabled = $model->DISTRITO ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'DISTRITO')->textInput($disabled) ?>

                    <?php $disabled = $model->DISTRITOLOCAL ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'DISTRITOLOCAL')->textInput($disabled) ?>

                    <?php $disabled = $model->CASILLA ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'CASILLA')->textInput($disabled) ?>

                    <?php $disabled = $model->SECCION ? ['disabled'=>'true'] : [] ?>
                    <?= $form->field($model, 'SECCION')->textInput($disabled) ?>

                    <div class="form-group">
                        <div class="col-md-12 text-center">
                        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Cancelar', Url::toRoute('padron/buscar', true), ['class' => 'btn btn-danger']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
