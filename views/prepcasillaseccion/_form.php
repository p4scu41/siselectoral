<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCasillaSeccion */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs('getByMuni = "'.Url::toRoute('prepseccion/getbymuni').'"', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/prepcasillaseccion.js'));
?>

<div class="prepcasilla-seccion-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <label for="Municipio">Municipio</label>
        <?= Html::dropDownList('municipio', $id_municipio, $municipios, ['prompt' => 'Seleccione un municipio', 'class' => 'form-control', 'id' => 'municipio']) ?>
    </div>

    <?= $form->field($model, 'id_seccion')->dropDownList($secciones, ['prompt' => 'Seleccione la sección']) ?>

    <?= $form->field($model, 'id_casilla')->dropDownList($casillas, ['prompt' => 'Seleccione el tipo de casilla']) ?>

    <?= $form->field($model, 'descripcion')->textInput() ?>

    <?= $form->field($model, 'colonia')->textInput() ?>

    <?= $form->field($model, 'domicilio')->textInput() ?>

    <?= $form->field($model, 'cp')->textInput() ?>

    <?= $form->field($model, 'localidad')->textInput() ?>

    <?= $form->field($model, 'repre_gral')->textInput() ?>

    <?= $form->field($model, 'tel_repre_gral')->textInput() ?>

    <?= $form->field($model, 'repre_casilla')->textInput() ?>

    <?= $form->field($model, 'tel_repre_casilla')->textInput() ?>

    <?= $form->field($model, 'observaciones')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?> &nbsp; 
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
