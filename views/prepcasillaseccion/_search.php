<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->registerJs('getByMuni = "'.Url::toRoute('prepseccion/getbymuni').'"', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/prepcasillaseccion.js'));

/* @var $this yii\web\View */
/* @var $model app\models\PREPCasillaSeccionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prepcasilla-seccion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'layout' => 'inline'
    ]); ?>

    <div class="form-group">
        <label for="Municipio">Municipio: </label>
        <?= Html::dropDownList('municipio', Yii::$app->request->get('municipio'), $municipios, ['prompt' => 'Seleccione un municipio', 'class' => 'form-control', 'id' => 'municipio']) ?>
    </div>

    <div class="form-group">
        <label for="Municipio">Sección: </label>
        <?= Html::activeDropDownList($model, 'id_seccion', $secciones, ['prompt' => 'Seleccione una sección', 'class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?> &nbsp; 
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
