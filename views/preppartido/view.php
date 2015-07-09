<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PREPPartido */

$this->title = 'Datos de ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="preppartido-view">

    <p>
        <?= Html::a('Ir al listado', ['index'], ['class' => 'btn btn-default']) ?> &nbsp; 
        <?= Html::a('Actualizar', ['update', 'id' => $model->id_partido], ['class' => 'btn btn-primary']) ?> &nbsp; 
        <?php /* echo Html::a('Eliminar', ['delete', 'id' => $model->id_partido], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Esta seguro que desea eliminar el registro?',
                'method' => 'post',
            ],
        ]) */ ?>
    </p>
    <?php
    if (!empty($logoPartido)) {
        echo '<div class="form-group">'.
            '<div class="text-center">'.
                '<img src="'.$logoPartido.'" class="img-rounded imgPerson" id="imgPerson">'.
            '</div>'.
        '</div>';
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nombre',
            'nombre_corto',
            'propietario',
            'suplente',
            'observaciones',
        ],
    ]) ?>

</div>
