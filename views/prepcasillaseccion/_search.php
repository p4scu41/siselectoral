<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCasillaSeccionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prepcasilla-seccion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_casilla_seccion') ?>

    <?= $form->field($model, 'id_seccion') ?>

    <?= $form->field($model, 'id_casilla') ?>

    <?= $form->field($model, 'descripcion') ?>

    <?= $form->field($model, 'colonia') ?>

    <?php // echo $form->field($model, 'domicilio') ?>

    <?php // echo $form->field($model, 'cp') ?>

    <?php // echo $form->field($model, 'localidad') ?>

    <?php // echo $form->field($model, 'repre_gral') ?>

    <?php // echo $form->field($model, 'tel_repre_gral') ?>

    <?php // echo $form->field($model, 'repre_casilla') ?>

    <?php // echo $form->field($model, 'tel_repre_casilla') ?>

    <?php // echo $form->field($model, 'observaciones') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
