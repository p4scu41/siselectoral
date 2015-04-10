<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PromocionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="promocion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'layout' => 'inline'
    ]); ?>

    <?= $form->field($model, 'IdPersonaPromueve', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('IdPersonaPromueve')]]) ?>

    <?= $form->field($model, 'IdPuesto', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('IdPuesto')]]) ?>

    <?= $form->field($model, 'IdPersonaPuesto', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('IdPersonaPuesto')]]) ?>

    <?php // echo $form->field($model, 'FechaPromocion') ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
