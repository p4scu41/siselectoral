<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PadronGlobal */

$this->title = 'Update Padron Global: ' . ' ' . $model->CLAVEUNICA;
$this->params['breadcrumbs'][] = ['label' => 'Padron Globals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->CLAVEUNICA, 'url' => ['view', 'id' => $model->CLAVEUNICA]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="padron-global-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
