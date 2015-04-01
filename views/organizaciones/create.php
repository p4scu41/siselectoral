<?php

//use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Organizaciones */

$this->title = 'Crear nueva Organización';
$this->params['breadcrumbs'][] = ['label' => 'Organizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizaciones-create">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'tipos' => $tipos,
    ]) ?>

</div>
