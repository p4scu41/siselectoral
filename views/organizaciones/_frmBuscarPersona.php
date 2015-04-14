<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJs('getSeccionesMuni="'.Url::toRoute('seccion/getbymunicipio', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('buscarPersona="'.Url::toRoute('padron/find', true).'";', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/frmBuscarPersona.js'));
?>

<div class="modal fade" id="modalBuscarPersona" tabindex="-1" role="dialog" aria-labelledby="modalBuscarPersonaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalBuscarPersonaLabel">Buscar Persona</h4>
            </div>
            <div class="modal-body">
                
                <?php
                echo Html::beginForm('#', 'POST', [
                        'name' => 'frmBuscarPersona',
                        'id' => 'frmBuscarPersona',
                        'class' => 'form-inline'
                    ]);

                $municipios = [0 => 'Seleccione el municipio'] + $municipios;

                echo '<div class="form-group">';
                echo Html::dropDownList('municipio', null, $municipios, [
                        'class' => 'form-control',
                        'id' => 'municipio',
                    ]);
                echo '</div>';

                echo '<div class="form-group">';
                echo Html::dropDownList('seccion', null, [0=>'Sección'], [
                        'class' => 'form-control',
                        'id' => 'seccion',
                    ]);
                echo '</div>';

                echo '<div class="form-group">';
                echo Html::textInput('nombre', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Nombre'
                    ]);
                echo '</div>';

                echo '<div class="form-group">';
                echo Html::textInput('apellidoPaterno', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Apellido Paterno'
                    ]);
                echo '</div>';

                echo '<div class="form-group">';
                echo Html::textInput('apellidoMaterno', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Apellido Materno'
                    ]);
                echo '</div>';

                echo '<br><div class="text-center">';
                echo Html::button('Buscar', [
                        'class' => 'btn btn-success',
                        'id' => 'btnBuscarPersona'
                    ]);
                echo '</div>';

                echo Html::endForm();
                ?>
                <br>
                <div class="table-responsive" id="resultBuscarPersona">
                    <table id="tblResultBuscarPersona" class="table table-condensed table-bordered table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Sección</th>
                                <th class="text-center">Casilla</th>
                                <th class="text-center">Dirección</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnAsignarPersona">Aceptar</button>
            </div>
        </div>
    </div>
</div>