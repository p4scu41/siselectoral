<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\PREPSeccion */

$this->title = 'Registrar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepseccion-create">

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios
    ]) ?>

</div>
