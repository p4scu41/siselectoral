<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PREPPartido */

$this->title = 'Actualizar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombre_corto, 'url' => ['view', 'id' => $model->id_partido]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="preppartido-update">

    <?= $this->render('_form', [
        'model' => $model,
        'logoPartido' => $logoPartido,
    ]) ?>

</div>
