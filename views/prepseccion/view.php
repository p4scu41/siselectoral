<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PREPSeccion */

$this->title = 'Datos de la ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepseccion-view">

    <p>
        <?= Html::a('Ir al listado', ['index'], ['class' => 'btn btn-default']) ?> &nbsp; 
        <?= Html::a('Actualizar', ['update', 'id' => $model->id_seccion], ['class' => 'btn btn-primary']) ?> &nbsp; 
        <?php /* echo Html::a('Eliminar', ['delete', 'id' => $model->id_seccion], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Esta seguro que desea eliminar el registro?',
                'method' => 'post',
            ],
        ]) */?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'municipio',
                'value' => $model->cmunicipio->DescMunicipio,
            ],
            'zona',
            'seccion',
            'distrito_local',
            'distrito_federal',
            'observaciones',
            'activo:boolean',
            'fecha_cierre',
        ],
    ]) ?>

</div>
