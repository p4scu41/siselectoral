<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCandidato */

$this->title = 'Actualizar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_candidato, 'url' => ['view', 'id' => $model->id_candidato]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="prepcandidato-update">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'partidos' => $partidos,
        'tiposEleccion' => $tiposEleccion,
    ]) ?>

</div>
