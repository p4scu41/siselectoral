
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\PadronGlobalSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <!-- left column -->
    <div class="col-lg-12">
        <!-- general form elements -->
        <div class="box box-primary box-success">

            <div class="panel panel-success" id="panelBuscar">
                <div class="panel-heading">
                    <h3 class="panel-title">Buscar Persona</h3>
                </div>
                <div class="panel-body">
                    <div class="padron-global-search">

                        <?php $form = ActiveForm::begin([
                            'action' => ['buscar'],
                            'method' => 'GET',
                            'options' => ['class' => 'form-inline'],
                        ]); ?>

                        <?php echo $form->field($model, 'MUNICIPIO')->dropDownList($municipios, ['prompt' => 'Elija una opción']) ?>

                        <?php echo $form->field($model, 'APELLIDO_PATERNO') ?>

                        <?php echo $form->field($model, 'APELLIDO_MATERNO') ?>

                        <?php echo $form->field($model, 'NOMBRE') ?>

                        <?php echo $form->field($model, 'SEXO')->dropDownList(['M'=>'Mujer', 'H'=>'Hombre'], ['prompt' => 'Elija una opción']) ?>

                        <p><div class="form-group">
                            <?= Html::submitButton('Buscar', ['class' => 'btn btn-success']) ?>
                            <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadIndicator"></i>
                        </div></p>

                        <?php ActiveForm::end(); ?>

                    </div>
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->