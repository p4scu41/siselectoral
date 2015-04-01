<?php

//use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Organizaciones */

$this->title = 'Actualizar datos de la Organización';
$this->params['breadcrumbs'][] = ['label' => 'Organizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->IdOrganizacion, 'url' => ['view', 'id' => $model->IdOrganizacion]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="organizaciones-update">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'tipos' => $tipos,
    ]) ?>

</div>
