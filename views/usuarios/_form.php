<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Url::to('@web/js/usuarios.js'));
?>

<div class="usuarios-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <?= Html::activeHiddenInput($model, 'IdEstructuraMov', ['value' => 1]) ?>

    <?= $form->field($model, 'IdPerfil')->dropDownList($perfiles, ['prompt' => 'Seleccione una opción']) ?>

    <?= Html::activeHiddenInput($model, 'IdPersona') ?>

    <?= $form->field($model, 'persona', [
            'inputOptions' => [
                'readonly' => 'true',
                'id' => 'persona',
                'value' => $model->persona ? $model->persona->nombreCompleto : ''
            ]
        ])->textInput() ?>

    <?= $form->field($model, 'login')->textInput() ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?php
        if ($model->isNewRecord) {
            echo Html::activeHiddenInput($model, 'Estado', ['value' => 1]);
        } else {
            echo $form->field($model, 'Estado')->checkbox() ;
        }
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php echo $this->render('@app/views/organizaciones/_frmBuscarPersona', ['municipios'=>$municipios]) ?>
