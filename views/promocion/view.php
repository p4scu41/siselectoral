<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Promocion */

$this->title = $model->IdEstructuraMov;
$this->params['breadcrumbs'][] = ['label' => 'Promocions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promocion-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'IdEstructuraMov' => $model->IdEstructuraMov, 'IdpersonaPromovida' => $model->IdpersonaPromovida], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'IdEstructuraMov' => $model->IdEstructuraMov, 'IdpersonaPromovida' => $model->IdpersonaPromovida], [
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
            'IdEstructuraMov',
            'IdpersonaPromovida',
            'IdPuesto',
            'IdPersonaPromueve',
            'IdPersonaPuesto',
            'FechaPromocion',
        ],
    ]) ?>

</div>
