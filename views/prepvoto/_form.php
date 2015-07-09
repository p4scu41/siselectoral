<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPVoto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prepvoto-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_candidato')->textInput() ?>

    <?= $form->field($model, 'id_casilla_seccion')->textInput() ?>

    <?= $form->field($model, 'no_votos')->textInput() ?>

    <?= $form->field($model, 'observaciones')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
