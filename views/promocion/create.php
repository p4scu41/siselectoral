<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Promocion */

$this->title = 'Registrar Promoción';
$this->params['breadcrumbs'][] = ['label' => 'Promoción', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promocion-create">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
    ]) ?>

</div>
