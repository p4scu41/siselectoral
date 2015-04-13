<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Organizaciones */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile(Url::to('@web/js/organizaciones.js'));
?>

<div class="organizaciones-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'Nombre')->textInput() ?>

    <?= $form->field($model, 'Siglas')->textInput() ?>

    <?= Html::activeHiddenInput($model, 'IdPersonaRepresentante') ?>
    <?= $form->field($model, 'representante', [
            'inputOptions' => [
                'readonly' => 'true',
                'id' => 'org-representante',
                'value' => $model->representante ? $model->representante->nombreCompleto : ''
            ]
        ])->textInput() ?>

    <?= Html::activeHiddenInput($model, 'IdPersonaEnlace') ?>
    <?= $form->field($model, 'enlace', [
            'inputOptions' => [
                'readonly' => 'true',
                'id' => 'org-enlace',
                'value' => $model->enlace ? $model->enlace->nombreCompleto : ''
            ]
        ])->textInput() ?>

    <?= $form->field($model, 'IdMunicipio')->dropDownList($municipios, ['prompt' => 'Elija una opción']) ?>

    <?= $form->field($model, 'Observaciones')->textInput() ?>

    <?= $form->field($model, 'idTipoOrganizacion')->dropDownList($tipos, ['prompt' => 'Elija una opción']) ?>

    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', Url::toRoute('organizaciones/index', true), ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php echo $this->render('_frmBuscarPersona', ['municipios'=>$municipios]) ?>