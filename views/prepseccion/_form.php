<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPSeccion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prepseccion-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <?= $form->field($model, 'municipio')->dropDownList($municipios, ['prompt' => 'Seleccione un municipio']) ?>

    <?= $form->field($model, 'zona')->textInput() ?>

    <?= $form->field($model, 'seccion')->textInput() ?>

    <?= $form->field($model, 'distrito_local')->textInput() ?>

    <?= $form->field($model, 'distrito_federal')->textInput() ?>

    <?= $form->field($model, 'observaciones')->textInput() ?>

    <?= $form->field($model, 'activo')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? "Guardar" : "Actualizar", ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
