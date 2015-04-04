<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrganizacionesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organizaciones-search">

    <h4>Opciones de búsqueda: </h4>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'layout' => 'inline',
    ]); ?>

    <?= $form->field($model, 'Nombre', ['inputOptions' => [ 'placeholder' => $model->getAttributeLabel('Nombre') ]]) ?>

    <?= $form->field($model, 'IdMunicipio')->dropDownList($municipios, ['prompt' => 'Municipio']) ?>

    <?= $form->field($model, 'idTipoOrganizacion')->dropDownList($tipos, ['prompt' => 'Tipo de organización']) ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Cancelar', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
