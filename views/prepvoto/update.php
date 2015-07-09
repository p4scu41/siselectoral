<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PREPVoto */

$this->title = 'Update Prepvoto: ' . ' ' . $model->id_partido;
$this->params['breadcrumbs'][] = ['label' => 'Prepvotos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_partido, 'url' => ['view', 'id_partido' => $model->id_partido, 'id_casilla_seccion' => $model->id_casilla_seccion]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prepvoto-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
