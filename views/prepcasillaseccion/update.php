<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCasillaSeccion */

$this->title = 'Actualizar Casilla de Sección: ' . ' ' . $model->descripcion;
$this->params['breadcrumbs'][] = ['label' => 'Casillas por Sección', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descripcion, 'url' => ['view', 'id' => $model->id_casilla_seccion]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="prepcasilla-seccion-update">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'id_municipio' => $id_municipio,
        'casillas' => $casillas,
        'secciones' => $secciones
    ]) ?>

</div>
