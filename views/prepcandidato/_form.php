<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCandidato */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile(Url::to('@web/js/prepcandidato.js'));

?>

<div class="prepcandidato-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <?= $form->field($model, 'id_partido')->dropDownList($partidos, ['prompt' => 'Seleccione un partido político']) ?>

    <?= $form->field($model, 'nombre')->textInput() ?>
    
    <?= $form->field($model, 'id_tipo_eleccion')->dropDownList($tiposEleccion, ['prompt' => 'Seleccione el tipo de elección']) ?>

    <?= $form->field($model, 'municipio', ['options' => ['class' => (empty($model->municipio) ? 'hidden' : '' )]])->dropDownList($municipios, ['prompt' => 'Seleccione un municipio']) ?>

    <?= $form->field($model, 'distrito_local', ['options' => ['class' => (empty($model->distrito_local) ? 'hidden' : '' )]])->textInput() ?>

    <?= $form->field($model, 'distrito_federal', ['options' => ['class' => (empty($model->distrito_federal) ? 'hidden' : '' )]])->textInput() ?>

    <?= $form->field($model, 'activo')->checkbox() ?>

    <?= $form->field($model, 'observaciones')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? "Guardar" : "Actualizar", ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
