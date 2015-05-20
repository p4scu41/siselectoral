<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PadronGlobal */

$this->title = 'Actualización de datos personales';
$this->params['breadcrumbs'][] = ['label' => 'Buscar', 'url' => ['padron/buscar']];
$this->params['breadcrumbs'][] = 'Actualización';
?>
<div class="padron-global-update">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'escolaridad' => $escolaridad,
        'ocupacion' => $ocupacion,
        'estado_civil' => $estado_civil,
        'observacion' => $observacion,
    ]) ?>

</div>
