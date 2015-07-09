<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPSeccionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prepseccion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'layout' => 'inline'
    ]); ?>

    <?= $form->field($model, 'municipio')->dropDownList($municipios, ['prompt' => 'Seleccione un municipio']) ?>

    <?= $form->field($model, 'zona', ['inputOptions'=> ['placeholder' => $model->getAttributeLabel('zona')]]) ?>

    <?= $form->field($model, 'distrito_local', ['inputOptions'=> ['placeholder' => $model->getAttributeLabel('distrito_local')]]) ?>

    <?php echo $form->field($model, 'distrito_federal', ['inputOptions'=> ['placeholder' => $model->getAttributeLabel('distrito_federal')]]) ?>

    <?php //echo $form->field($model, 'observaciones') ?>

    <?php // echo $form->field($model, 'activo') ?>

    <?php // echo $form->field($model, 'fecha_cierre') ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
