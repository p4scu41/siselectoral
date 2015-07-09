<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PREPCasilla */

$this->title = 'Datos de ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepcasilla-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ir al listado', ['index'], ['class' => 'btn btn-default']) ?> &nbsp; 
        <?= Html::a('Actualizar', ['update', 'id' => $model->id_casilla], ['class' => 'btn btn-primary']) ?> &nbsp; 
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id_casilla], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Esta seguro que desea eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_casilla',
            'descripcion',
        ],
    ]) ?>

</div>
