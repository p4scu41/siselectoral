<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCasilla */

$this->title = 'Actualizar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descripcion, 'url' => ['view', 'id' => $model->id_casilla]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="prepcasilla-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
