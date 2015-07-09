<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPPartidoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="preppartido-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_partido') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'nombre_corto') ?>

    <?= $form->field($model, 'propietario') ?>

    <?= $form->field($model, 'suplente') ?>

    <?php // echo $form->field($model, 'observaciones') ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
