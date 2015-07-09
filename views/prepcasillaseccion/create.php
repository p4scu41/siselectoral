<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCasillaSeccion */

$this->title = 'Asignar Casilla a Sección';
$this->params['breadcrumbs'][] = ['label' => 'Casillas por Sección', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepcasilla-seccion-create">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'id_municipio' => $id_municipio,
        'casillas' => $casillas,
        'secciones' => $secciones
    ]) ?>

</div>
