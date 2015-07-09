<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCasillaSeccion */

$this->title = 'Datos de la Casilla asignada a la Sección';
$this->params['breadcrumbs'][] = ['label' => 'Casillas por Sección', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepcasilla-seccion-view">

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id_casilla_seccion], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Regresar a listado', ['index'], ['class' => 'btn btn-default']) ?>
        <?php /* echo Html::a('Delete', ['delete', 'id' => $model->id_casilla_seccion], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id_seccion',
                'value' => $model->seccion->seccion
            ],
            [
                'attribute' => 'id_casilla',
                'value' => $model->casilla->descripcion
            ],
            'descripcion',
            'colonia',
            'domicilio',
            'cp',
            'localidad',
            'repre_gral',
            'tel_repre_gral',
            'repre_casilla',
            'tel_repre_casilla',
            'observaciones',
        ],
    ]) ?>

</div>
