<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\PromocionSearch */
/* @var $form yii\widgets\ActiveForm */

$url = \yii\helpers\Url::to(['promocion/findactivistaspromocion']);
$this->registerJs('urlOtrosPromocion = "'.Url::toRoute('promocion/findotrospromocion').'";', \yii\web\View::POS_HEAD);

// Script to initialize the selection based on the value of the select2 element
$initScript = <<< SCRIPT
function (element, callback) {
    var id = \$(element).val();

    if (id == undefined) {
        id = null;
    }

    var persona_promueve = \$(element).attr("id") == "persona_promueve" ? 1 : 0;

    \$.ajax("{$url}", {
        dataType: "json",
        method: "POST",
        data: { id: id, personaPromueve: persona_promueve }
    }).done(function(data) { if (data.length > 0) { callback(data[0]); } });
}
SCRIPT;
?>

<div class="promocion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => 'frmSearchPromocion'
    ]); ?>
 
    <!-- <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Zonas: </label>
                <select name="zona" id="zona" class="form-control">
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Secciones: </label>
                <select name="seccion" id="seccion" class="form-control">
                </select>
            </div>
        </div>
    </div> -->

    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <?PHP
                echo '<input type="hidden" name="PromocionSearch[personaPromueveZona]" value="'.($model->personaPromueve ? $model->zona : '').'">';
                echo '<input type="hidden" name="PromocionSearch[personaPromueveSeccion]" value="'.($model->personaPromueve ? $model->seccional : '').'">';

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
                                . 'return { nombre:term, personaPromueve: 1 }; '
                                . '}'),
                            'results' => new JsExpression('function(data,page) { return {results:data}; }'),
                        ],
                        'initSelection' => new JsExpression($initScript),
                        'formatResult' => new JsExpression('function (result) { return (result.Descripcion ? result.Descripcion : "") + " " + result.NombreCompleto; }'),
                        'formatSelection' => new JsExpression('function (selection) { '
                            . 'cargaOtrasPromociones(selection.id); '
                            . 'return (selection.Descripcion ? selection.Descripcion : "") + " " + selection.NombreCompleto; '
                            . '}')
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-6">
            <?PHP
            echo $form->field($model, 'IdPersonaPuesto')->widget(Select2::classname(), [
                'options' => [
                    'placeholder' => 'Puesto en donde promueve',
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
                            . 'return { nombre:term, personaPromueve: 0 }; '
                            . '}'),
                        'results' => new JsExpression('function(data,page) { return {results:data}; }'),
                    ],
                    'initSelection' => new JsExpression($initScript),
                    'formatResult' => new JsExpression('function (result) { return (result.Descripcion ? result.Descripcion : "") + " " + result.NombreCompleto; }'),
                    'formatSelection' => new JsExpression('function (selection) { '
                        . ' $("#promocionsearch-idpersonapuesto").val(selection.CLAVEUNICA); '
                        . 'return (selection.Descripcion ? selection.Descripcion : "") + " " + selection.NombreCompleto; '
                        . '}')
                ],
            ]);
            ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
            <?= $form->field($model, 'FechaPromocion')->widget(DateControl::classname(), [
                            'displayFormat' => 'dd/MM/yyyy',
                            'saveFormat' => 'yyyy-MM-dd',
                            'autoWidget' => false,
                            'widgetClass' => 'yii\widgets\MaskedInput',
                            'options' => [
                                'mask' => '99/99/9999',
                                'options' => ['class'=>'form-control', 'placeholder'=>'dd/mm/aaaa']
                            ],
                        ]);
                    ?>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Puestos promovidos: </label>
                    <select name="puestos_promovidos" id="puestos_promovidos" class="form-control">
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <?= Html::submitButton('Buscar', ['class' => 'btn bg-darkred']) ?>
                <?= Html::a('Limpiar', ['index'], ['class' => 'btn btn-danger']) ?>
                <a href="#" id="btnExportPdf" data-url="<?= Url::to(['promocion/pdf'], true) ?>" class="btn btn-default">
                    <i class="fa fa-file-pdf-o"></i> Exportar a pdf
                </a>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
