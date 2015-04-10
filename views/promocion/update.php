<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Promocion */

$this->title = 'Actualizar Promoción';
$this->params['breadcrumbs'][] = ['label' => 'Promocions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->IdEstructuraMov, 'url' => ['view', 'IdEstructuraMov' => $model->IdEstructuraMov, 'IdpersonaPromovida' => $model->IdpersonaPromovida]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="promocion-update">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
    ]) ?>

</div>
