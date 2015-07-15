<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPPartido */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="preppartido-form">

    <?php 
        $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => ['enctype' => 'multipart/form-data']
        ]);

        if (!empty($logoPartido)) {
            echo '<div class="form-group">'.
                '<div class="text-center">'.
                    '<img src="'.$logoPartido.'" class="img-rounded imgPerson" id="imgPerson">'.
                '</div>'.
            '</div>';
        }
    ?>
    
    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <?= $form->field($model, 'nombre')->textInput() ?>

    <?= $form->field($model, 'nombre_corto')->textInput() ?>

    <?= $form->field($model, 'propietario')->textInput() ?>

    <?= $form->field($model, 'suplente')->textInput() ?>

    <?php
        echo '<div class="form-group"><label class="control-label col-sm-3" for="logo">Logo</label><div class="col-sm-6">';
    
        echo Html::fileInput('logo', null, ['id' => 'logo', 'class'=>'file',
            'data-browse-label'=> 'Seleccionar Logo',
            'data-browse-icon'=>'<i class="glyphicon glyphicon-picture"></i> &nbsp;',
            'data-remove-label'=> "Eliminar",
            'data-allowed-file-extensions'=> '["jpg", "jpeg", "png", "gif"]',
            'data-show-upload'=> 'false',
            'data-msg-invalid-file-extension'=> 'Extensión no permitida en el archivo seleccionado "{name}". Solo se permiten las extensiones "{extensions}"',
        ]);

        echo '</div></div>';
    ?>

    <?= $form->field($model, 'observaciones')->textInput() ?>

    <?= $form->field($model, 'color')->textInput() ?>

    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? "Guardar" : "Actualizar", ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?> &nbsp; 
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
