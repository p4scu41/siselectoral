<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\PadronGlobal */
/* @var $form yii\widgets\ActiveForm */
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

                    <?= $form->field($model, 'foto')->fileInput(['class'=>'file',
                        'data-browse-label'=> 'Seleccionar Foto',
                        'data-browse-icon'=>'<i class="glyphicon glyphicon-picture"></i> &nbsp;',
                        'data-remove-label'=> "Eliminar",
                        'data-allowed-file-extensions'=> '["jpg", "jpeg", "png", "gif"]',
                        'data-show-upload'=> 'false',
                        'data-msg-invalid-file-extension'=> 'Extensión no permitida en el archivo seleccionado "{name}". Solo se permiten las extensiones "{extensions}"',
                        ]) ?>

                    <?= $form->field($model, 'APELLIDO_PATERNO', [
                            //'template' => "{label}\n<i class='fa fa-user'></i>\n{input}\n{hint}\n{error}"
                            //'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
                        ])->textInput() ?>

                    <?= $form->field($model, 'APELLIDO_MATERNO')->textInput() ?>

                    <?= $form->field($model, 'NOMBRE')->textInput() ?>

                    <?= //$form->field($model, 'FECHA_NACI_CLAVE_ELECTORAL')->input('date')
                        $form->field($model, 'FECHA_NACI_CLAVE_ELECTORAL')->widget(DateControl::classname(), [
                            'displayFormat' => 'dd/MM/yyyy',
                            'saveFormat' => 'yyyy-MM-dd',
                            'autoWidget' => false,
                            'widgetClass' => 'yii\widgets\MaskedInput',
                            'options' => [
                                'mask' => '99/99/9999',
                                'options' => ['class'=>'form-control'],
                            ],
                        ]);
                    ?>

                    <?= $form->field($model, 'SEXO')->dropDownList(['M'=>'Mujer', 'H'=>'Hombre'], ['prompt' => 'Elija una opción']) ?>

                    <?= $form->field($model, 'MUNICIPIO')->dropDownList($municipios, ['prompt' => 'Elija una opción']) ?>

                    <?= $form->field($model, 'LOCALIDAD')->textInput() ?>

                    <?= $form->field($model, 'DOMICILIO')->textInput() ?>

                    <?= $form->field($model, 'NUM_INTERIOR')->textInput() ?>

                    <?= $form->field($model, 'NUM_EXTERIOR')->textInput() ?>

                    <?= $form->field($model, 'MANZANA')->textInput() ?>

                    <?= $form->field($model, 'CODIGO_POSTAL')->textInput() ?>

                    <?= $form->field($model, 'COLONIA')->textInput() ?>

                    <?= $form->field($model, 'DES_LOC')->textInput() ?>

                    <?= $form->field($model, 'NOM_LOC')->textInput() ?>

                    <?= $form->field($model, 'LUGAR_NACIMIENTO')->textInput() ?>

                    <?= $form->field($model, 'CORREOELECTRONICO')->textInput() ?>

                    <?= $form->field($model, 'TELMOVIL')->textInput() ?>

                    <?= $form->field($model, 'TELCASA')->textInput() ?>

                    <?= $form->field($model, 'DISTRITO')->textInput() ?>

                    <?= $form->field($model, 'DISTRITOLOCAL')->textInput() ?>

                    <?= $form->field($model, 'CASILLA')->textInput() ?>

                    <?= $form->field($model, 'SECCION')->textInput() ?>

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
