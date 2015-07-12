<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Actualizar Usuario: ' . ' ' . $model->login;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->IdUsuario, 'url' => ['view', 'id' => $model->IdUsuario]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="usuarios-update">

    <?= $this->render('_form', [
        'model' => $model,
        'perfiles' => $perfiles,
        'municipios' => $municipios,
    ]) ?>

</div>
