<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\PREPPartido */

$this->title = 'Registrar ' . $titulo_sin;
$this->params['breadcrumbs'][] = ['label' => $titulo_plu, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="preppartido-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
