<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PadronGlobal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="padron-global-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'CLAVEUNICA')->textInput() ?>

    <?= $form->field($model, 'CONS_ALF_POR_SECCION')->textInput() ?>

    <?= $form->field($model, 'ALFA_CLAVE_ELECTORAL')->textInput() ?>

    <?= $form->field($model, 'FECHA_NACI_CLAVE_ELECTORAL')->textInput() ?>

    <?= $form->field($model, 'LUGAR_NACIMIENTO')->textInput() ?>

    <?= $form->field($model, 'SEXO')->textInput() ?>

    <?= $form->field($model, 'DIGITO_VERIFICADOR')->textInput() ?>

    <?= $form->field($model, 'CLAVE_HOMONIMIA')->textInput() ?>

    <?= $form->field($model, 'NOMBRES')->textInput() ?>

    <?= $form->field($model, 'NOMBRE')->textInput() ?>

    <?= $form->field($model, 'APELLIDO_PATERNO')->textInput() ?>

    <?= $form->field($model, 'APELLIDO_MATERNO')->textInput() ?>

    <?= $form->field($model, 'CALLE')->textInput() ?>

    <?= $form->field($model, 'NUM_INTERIOR')->textInput() ?>

    <?= $form->field($model, 'NUM_EXTERIOR')->textInput() ?>

    <?= $form->field($model, 'COLONIA')->textInput() ?>

    <?= $form->field($model, 'CODIGO_POSTAL')->textInput() ?>

    <?= $form->field($model, 'FOLIO_NACIONAL')->textInput() ?>

    <?= $form->field($model, 'EN_LISTA_NOMINAL')->textInput() ?>

    <?= $form->field($model, 'ENTIDAD')->textInput() ?>

    <?= $form->field($model, 'DISTRITO')->textInput() ?>

    <?= $form->field($model, 'MUNICIPIO')->textInput() ?>

    <?= $form->field($model, 'SECCION')->textInput() ?>

    <?= $form->field($model, 'LOCALIDAD')->textInput() ?>

    <?= $form->field($model, 'MANZANA')->textInput() ?>

    <?= $form->field($model, 'NUM_EMISION_CREDENCIAL')->textInput() ?>

    <?= $form->field($model, 'DISTRITOLOCAL')->textInput() ?>

    <?= $form->field($model, 'CORREOELECTRONICO')->textInput() ?>

    <?= $form->field($model, 'TELMOVIL')->textInput() ?>

    <?= $form->field($model, 'TELCASA')->textInput() ?>

    <?= $form->field($model, 'CASILLA')->textInput() ?>

    <?= $form->field($model, 'IDPADRON')->textInput() ?>

    <?= $form->field($model, 'DOMICILIO')->textInput() ?>

    <?= $form->field($model, 'DES_LOC')->textInput() ?>

    <?= $form->field($model, 'NOM_LOC')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
