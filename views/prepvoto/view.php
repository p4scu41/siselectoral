<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PREPVoto */

$this->title = $model->id_partido;
$this->params['breadcrumbs'][] = ['label' => 'Prepvotos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepvoto-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id_partido' => $model->id_partido, 'id_casilla_seccion' => $model->id_casilla_seccion], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id_partido' => $model->id_partido, 'id_casilla_seccion' => $model->id_casilla_seccion], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_partido',
            'id_casilla_seccion',
            'no_votos',
            'observaciones',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
    ]) ?>

</div>
