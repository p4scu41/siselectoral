<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Promocion */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Url::to('@web/js/promocion.js'));

$url = \yii\helpers\Url::to(['promocion/getlistnodos']);

// Script to initialize the selection based on the value of the select2 element
$initScript = <<< SCRIPT
function (element, callback) {
    var id = \$(element).val();

    if (id == undefined) {
        if (\$("#promocionsearch-idpersonapromueve").val() != "") {
            id = \$("#promocionsearch-idpersonapromueve").val();
        } else {
            id = null;
        }
    }

    \$.ajax("{$url}", {
        dataType: "json",
        method: "POST",
        data: { id: id }
    }).done(function(data) { if (data.length > 0) { callback(data[0]); } });
}
SCRIPT;
?>

<div class="promocion-form">

    <?php $form = ActiveForm::begin(['id'=>'frmPromocion']);

        echo $form->errorSummary($model, ['class'=>'alert alert-danger']);

        echo '<div class="form-group"><label class="control-label" for="municipio_promocion">Municipio</label>';
        $muniSelected = null;

        /*if (strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idCapturista'])) {
            $muniSelected = Yii::$app->user->identity->persona->MUNICIPIO;
        }*/

        echo Html::dropDownList('municipio_promocion', $muniSelected, ([0=>'Seleccione una opción']+$municipios), [
                'class' => 'form-control',
                'id' => 'municipio_promocion',
            ]);
        echo '</div>';
    ?>

    <?= Html::activeHiddenInput($model, 'IdEstructuraMov', ['value' => 1]) ?>

    <div class="form-group"><label class="control-label" for="seccion_promocion">Sección</label>
        <?= Html::dropDownList('seccion_promocion', null, [], 
            ['prompt' => 'Seleccione la sección', 'class' => 'form-control', 'id' => 'seccion_promocion']) ?>
    </div>

    <?= Html::activeHiddenInput($model, 'IdpersonaPromovida') ?>
    <?= $form->field($model, 'personaPromovida', [
            'inputOptions' => [
                'readonly' => 'true',
                'id' => 'personaPromovida',
                'value' => $model->personaPromovida ? $model->personaPromovida->nombreCompleto : ''
            ]
        ])->textInput() ?>

    <div class="form-group">
        <label>Persona que promueve</label>
        <div class="row">
            <div class="col-md-4">
                <?PHP
                echo Select2::widget([
                    'options' => [
                        'placeholder' => 'Puesto que promueve (CM, CZ, CS, JS, CP, PR)',
                        'id' => 'desc_puesto_promueve'
                    ],
                    'name' => 'desc_puesto_persona_promueve',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 2,
                        'ajax' => [
                            'url' => $url,
                            'type' => 'POST',
                            'dataType' => 'json',
                            'data' => new JsExpression('function(term,page) { '
                                . 'return { puesto:term, '
                                . 'municipio: $("#municipio_promocion").val(), '
                                //. 'seccion: $("#seccion_promocion").val() }; '
                                . '}'),
                            'results' => new JsExpression('function(data,page) { return {results:data}; }'),
                        ],
                        'initSelection' => new JsExpression($initScript),
                        'formatResult' => new JsExpression('function (result) { return result.Descripcion + " " + result.NombreCompleto; }'),
                        'formatSelection' => new JsExpression('function (selection) { '
                            . ' $("#promocion-idpersonapromueve").val(selection.CLAVEUNICA); '
                            . 'return selection.Descripcion; '
                            . '}'),
                    ],
                    'pluginEvents' => [
                        'change' => 'function(event) { '
                            . 'if(event.added != undefined) { '
                            . '$("#persona_promueve").select2("val", event.added.CLAVEUNICA); } '
                            . '$("#resultValidacion").html(""); }',
                        'select2-removed' => 'function() { $("#promocion-idpersonapromueve").val(""); $("#persona_promueve").select2("val", ""); }',
                        'select2-loaded' => 'function() { $("#desc_puesto_persona_promueve").select2("val", $("#promocion-idpersonapromueve").val()); }',
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-8">
                <?PHP
                echo Select2::widget([
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
                                . 'return { nombre:term, '
                                . 'municipio: $("#municipio_promocion").val(), '
                                //. 'seccion: $("#seccion_promocion").val() }; '
                                . '}'),
                            'results' => new JsExpression('function(data,page) { return {results:data}; }'),
                        ],
                        'initSelection' => new JsExpression($initScript),
                        'formatResult' => new JsExpression('function (result) { return result.Descripcion + " " + result.NombreCompleto; }'),
                        'formatSelection' => new JsExpression('function (selection) { '
                            . ' $("#promocion-idpersonapromueve").val(selection.CLAVEUNICA); '
                            . 'return selection.NombreCompleto; '
                            . '}'),
                    ],
                    'pluginEvents' => [
                        'change' => 'function(event) { '
                            . 'if(event.added != undefined) { '
                            . '$("#desc_puesto_promueve").select2("val", event.added.CLAVEUNICA); } '
                            . '$("#resultValidacion").html(""); }',
                        'select2-removed' => 'function(event) { $("#promocion-idpersonapromueve").val(""); $("#desc_puesto_promueve").select2("val", ""); }',
                        'select2-loaded' => 'function() { $("#persona_promueve").select2("val", $("#promocion-idpersonapromueve").val()); }',
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'IdPersonaPromueve') ?>

    <div class="form-group">
        <label>Puesto en donde promueve</label>
        <div class="row">
            <!-- <div class="col-md-4">
                <?PHP
                /*echo Select2::widget([
                    'options' => [
                        'placeholder' => 'Puesto en donde promueve (CM, CZ, CS, JS, CP, PR)',
                        'id' => 'puesto'
                    ],
                    'name' => 'puesto',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 2,
                        'ajax' => [
                            'url' => $url,
                            'type' => 'POST',
                            'dataType' => 'json',
                            'data' => new JsExpression('function(term,page) { '
                                . 'return { puesto:term, '
                                . 'municipio: $("#municipio_promocion").val(), '
                                . 'seccion: $("#seccion_promocion").val() }; '
                                . '}'),
                            'results' => new JsExpression('function(data,page) { return {results:data}; }'),
                        ],
                        'initSelection' => new JsExpression($initScript),
                        'formatResult' => new JsExpression('function (result) { return result.Descripcion + " " + result.NombreCompleto; }'),
                        'formatSelection' => new JsExpression('function (selection) { '
                            . ' $("#promocion-idpuesto").val(selection.IdNodoEstructuraMov); '
                            . ' $("#promocion-idpersonapuesto").val(selection.CLAVEUNICA); '
                            . 'return selection.Descripcion; '
                            . '}'),
                    ],
                    'pluginEvents' => [
                        'change' => 'function(event) { '
                            . 'if(event.added != undefined) { '
                            . '$("#persona_puesto").select2("val", event.added.CLAVEUNICA); } '
                            . '$("#resultValidacion").html(""); }',
                        'select2-removed' => 'function() { $("#promocion-idpuesto, #promocion-idpersonapuesto").val(""); $("#persona_puesto").select2("val", ""); }',
                        'select2-loaded' => 'function() { $("#puesto").select2("val", $("#promocion-idpuesto").val()); }',
                    ]
                ]);*/
                ?>
            </div>
            <div class="col-md-8">-->
            <div class="col-md-12">
                <?PHP
                echo Select2::widget([
                    'options' => [
                        'placeholder' => 'Nombre del Promotor', //Persona asignada al Puesto en donde promueve
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
                                . 'return { nombre:term, '
                                . 'municipio: $("#municipio_promocion").val(), '
                                . 'puesto: "PR", '
                                //. 'seccion: $("#seccion_promocion").val() }; '
                                . '}'),
                            'results' => new JsExpression('function(data,page) { return {results:data}; }'),
                        ],
                        'initSelection' => new JsExpression($initScript),
                        'formatResult' => new JsExpression('function (result) { return result.Descripcion + " " + result.NombreCompleto; }'),
                        'formatSelection' => new JsExpression('function (selection) { '
                            . ' $("#promocion-idpuesto").val(selection.IdNodoEstructuraMov); '
                            . ' $("#promocion-idpersonapuesto").val(selection.CLAVEUNICA); '
                            . 'return selection.NombreCompleto; '
                            . '}'),
                    ],
                    'pluginEvents' => [
                        'change' => 'function(event) { '
                            . 'if(event.added != undefined) { '
                            . '$("#puesto").select2("val", event.added.CLAVEUNICA); } '
                            . '$("#resultValidacion").html(""); }',
                        'select2-removed' => 'function(event) { $("#promocion-idpuesto, #promocion-idpersonapuesto").val(""); /*$("#puesto").select2("val", "");*/ }',
                        'select2-loaded' => 'function() { $("#persona_puesto").select2("val", $("#promocion-idpuesto").val()); }',
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'IdPuesto') ?>

    <?= Html::activeHiddenInput($model, 'IdPersonaPuesto') ?>

    <?= Html::activeHiddenInput($model, 'FechaPromocion', ['value' => date('Y-m-d H:i:s')]) ?>

    <div class="form-group">
        <div id="resultValidacion"></div>
    </div>

    <div class="form-group">
        <?= Html::button($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'btnSendForm']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php echo $this->render('@app/views/organizaciones/_frmBuscarPersona', ['municipios'=>$municipios]) ?>