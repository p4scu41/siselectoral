<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PadronGlobal */

$this->title = 'Registro';
$this->params['breadcrumbs'][] = ['label' => 'Persona', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="padron-global-create">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'escolaridad' => $escolaridad,
        'ocupacion' => $ocupacion,
        'estado_civil' => $estado_civil
    ]) ?>

</div>
