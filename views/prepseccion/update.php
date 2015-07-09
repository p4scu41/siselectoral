<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PREPSeccion */

$this->title = 'Actualizar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->seccion, 'url' => ['view', 'id' => $model->id_seccion]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="prepseccion-update">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios
    ]) ?>

</div>
