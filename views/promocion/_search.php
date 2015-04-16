<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\PromocionSearch */
/* @var $form yii\widgets\ActiveForm */

$url = \yii\helpers\Url::to(['promocion/getlistnodos']);

// Script to initialize the selection based on the value of the select2 element
$initScript = <<< SCRIPT
function (element, callback) {
    var id = \$(element).val();

    if (id == undefined) {
        id = null;
    }

    \$.ajax("{$url}", {
        dataType: "json",
        method: "POST",
        data: { id: id }
    }).done(function(data) { if (data.length > 0) { callback(data[0]); } });
}
SCRIPT;
?>

<div class="promocion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group">
        <div class="row">
            <div class="col-md-5">
                <?PHP
                echo $form->field($model, 'IdPersonaPromueve')->widget(Select2::classname(), [
                    'options' => [
                        'placeholder' => 'Nombre Persona que promueve',
                        'id' => 'persona_promueve'
                    ],
                    'name' => 'persona_promueve',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'ajax' => [
                            'url' => $url,
                            'type' => 'POST',
                            'dataType' => 'json',
                            'data' => new JsExpression('function(term,page) { '
                                . 'return { nombre:term }; '
                                . '}'),
                            'results' => new JsExpression('function(data,page) { return {results:data}; }'),
                        ],
                        'initSelection' => new JsExpression($initScript),
                        'formatResult' => new JsExpression('function (result) { return result.Descripcion + " " + result.NombreCompleto; }'),
                        'formatSelection' => new JsExpression('function (selection) { '
                            . 'return selection.Descripcion + " " + selection.NombreCompleto; '
                            . '}')
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-5">
            <?PHP
            echo $form->field($model, 'IdPersonaPuesto')->widget(Select2::classname(), [
                'options' => [
                    'placeholder' => 'Nombre Persona asignada al Puesto en donde promueve',
                    'id' => 'persona_puesto'
                ],
                'name' => 'persona_puesto',
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => $url,
                        'type' => 'POST',
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { '
                            . 'return { nombre:term }; '
                            . '}'),
                        'results' => new JsExpression('function(data,page) { return {results:data}; }'),
                    ],
                    'initSelection' => new JsExpression($initScript),
                    'formatResult' => new JsExpression('function (result) { return result.Descripcion + " " + result.NombreCompleto; }'),
                    'formatSelection' => new JsExpression('function (selection) { '
                        . ' $("#promocionsearch-idpersonapuesto").val(selection.CLAVEUNICA); '
                        . 'return selection.Descripcion + " " + selection.NombreCompleto; '
                        . '}')
                ],
            ]);
            ?>
            </div>
            <div class="col-md-2">
            <?= $form->field($model, 'FechaPromocion')->widget(DateControl::classname(), [
                            'displayFormat' => 'dd/MM/yyyy',
                            'saveFormat' => 'yyyy-MM-dd',
                            'autoWidget' => false,
                            'widgetClass' => 'yii\widgets\MaskedInput',
                            'options' => [
                                'mask' => '99/99/9999',
                                'options' => ['class'=>'form-control']
                            ],
                        ]);
                    ?>
            </div>
            <div class="col-md-12">
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Limpiar', ['index'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
