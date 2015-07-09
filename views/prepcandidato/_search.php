<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCandidatoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prepcandidato-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_candidato') ?>

    <?= $form->field($model, 'id_partido') ?>

    <?= $form->field($model, 'municipio') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'distrito_local') ?>

    <?php // echo $form->field($model, 'distrito_federal') ?>

    <?php // echo $form->field($model, 'activo') ?>

    <?php // echo $form->field($model, 'observaciones') ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
