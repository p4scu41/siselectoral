<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\PREPVoto */

$this->title = 'Create Prepvoto';
$this->params['breadcrumbs'][] = ['label' => 'Prepvotos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepvoto-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
