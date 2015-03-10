<?php

use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'Detalles estructura';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Persona';
$this->registerJsFile('http://maps.google.com/maps/api/js?sensor=false');
$this->registerJsFile(Url::to('@web/js/plugins/json-to-table.js'));
$this->registerJsFile(Url::to('@web/js/persona.js'));
$this->registerJs('puesto = "'.$puesto.'";'.
            'municipio = "'.$persona->MUNICIPIO.'";'.
            'nodo = "'.$nodo.'";'
        , \yii\web\View::POS_HEAD);
$this->registerJs('urlResumenNodo="'.Url::toRoute('site/getresumennodo', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetProgramas="'.Url::toRoute('site/getprogramas', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlGetIntegrantes="'.Url::toRoute('site/getintegrantesprogbyseccion', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlListInte="'.Url::toRoute('organizacion/listintegrantesfromseccion').'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlBranch="'.Url::toRoute('site/getbranch', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlTree="'.Url::toRoute('site/gettree', true).'";', \yii\web\View::POS_HEAD);
$this->registerCssFile(Url::to('@web/css/fancytree/skin-win8-n/ui.fancytree.css'));

?>
<div class="row">
    <!-- left column -->
    <div class="col-lg-12">
        <!-- general form elements -->
        <div class="box box-primary box-success">

            <div class="panel panel-success" id="containerPerson">
                <div class="panel-heading">
                    <h3 class="panel-title inline">Datos Personales</h3>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <?php
                        $form = ActiveForm::begin([
                                'options' => ['class' => 'form-inline'],
                                'method' => 'POST',
                                'action' => $actionPersona
                            ]);
                            echo '<input type="hidden" name="back" id="back" value="1">';
                            ?>
                        <button type="submit" class="btn btn-success pull-right btn-sm"><i class="fa fa-mail-reply"></i> Regresar</button>
                        <?php ActiveForm::end(); ?>
                        <br><br>
                        <div class="col-md-4 text-center">
                            <div>
                                <img src="<?= $persona ? $persona->getFoto() : ''; ?>" class="img-rounded imgPerson" id="imgPerson">
                                <h4 id="nombreCompleto"><?= ($persona->APELLIDO_PATERNO.' '.$persona->APELLIDO_MATERNO.' '.$persona->NOMBRE); ?></h4>
                            </div>
                            <div>
                                <span class="btn btn-app btn-sm btn-primary" id="meta_proyec">
                                    <span class="line-height-1 bigger-170" id="no_meta_proyec"><?= $no_meta_proyec ?></span>
                                    <br>
                                    <span class=""> Meta Proyec </span>
                                </span>
                                <span class="btn btn-app btn-sm btn-yellow">
                                    <span class="line-height-1 bigger-170"><?= (int)$numDepend['cantidad'] ?></span>
                                    <br>
                                    <span class="puesto"><?= ($numDepend['puesto'] != '' ? $numDepend['puesto'] : 'Sin Dependencias') ?></span>
                                </span>
                                <span class="btn btn-app btn-sm btn-success" id="meta">
                                    <span class="line-height-1 bigger-170" id="no_meta"><?= $no_meta_estruc ?>%</span>
                                    <br><span>Meta Estruc.</span>
                                </span>
                                <span class="btn btn-app btn-sm btn-purple" id="meta_promocion">
                                    <span class="line-height-1 bigger-170" id="no_meta_promocion"><?= $no_meta_promocion ?>%</span>
                                    <br>
                                    <span class="">Promoción</span>
                                </span>
                                <span class="btn btn-app btn-sm btn-pink" id="btn_programas">
                                    <span class="line-height-1 bigger-170" id="no_programas"> 0 </span>
                                    <br>
                                    <span class=""> Programas </span>
                                </span>
                                <span class="btn btn-app btn-sm btn-grey" id="infoEstrucAlterna">
                                    <span class="line-height-1 bigger-170"> 0 </span>
                                    <br>
                                    <span class=""> Estruc. Alterna </span>
                                </span>
                            </div>
                            <br>

                            <div class="table-responsive" id="resumenNodo" style="display: none;">
                                <i class="fa fa-refresh fa-spin" style="font-size: x-large;"></i>
                            </div>
                            <div id="seccion_promocion" style="display: none;"></div>
                            <div id="seccion_programas" style="display: none;">
                                Programas disponibles en el municipio:
                                <div id="list_programas"></div>
                                <div id="list_integrantes" class="tblListIntegrantesBySeccion" style="display: none;"></div>
                            </div>
                            <div class="table-responsive">
                                <table id="treeEstrucAlterna" class="table table-condensed table-striped table-bordered table-hover" style="display: none">
                                    <colgroup>
                                        <col width="*"></col>
                                        <col width="80px"></col>
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th class="text-center">Puesto</th>
                                            <th class="text-center">Asignaci&oacute;n</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="col-md-8">
                            <form class="form-horizontal" method="POST" id="frmPersonDetails">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Puesto</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($puesto) ? $puesto : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Domicilio</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($persona->CALLE) ? $persona->CALLE : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">E-mail</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($persona->CORREOELECTRONICO) ? $persona->CORREOELECTRONICO : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tel. Casa</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($persona->TELCASA) ? $persona->TELCASA : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tel. Móvil</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($persona->TELMOVIL) ? $persona->TELMOVIL : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Sección</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($persona->SECCION) ? $persona->SECCION : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Casilla</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($persona->CASILLA) ? $persona->CASILLA : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Género</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($persona->SEXO) ? $persona->genero : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Distrito</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($persona->DISTRITO) ? $persona->DISTRITO: '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <?php if ($secciones) { ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Secciones coordinadas</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= $secciones; ?></div>
                                    </div>
                                </div>
                                <?php } ?>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->

<!--<div class="text-center">
    <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modalMap">
        <span class="glyphicon glyphicon-globe"></span>
        <span class="fa fa-map-marker"></span> Ver Mapa
    </button>
</div>
<br>-->

<!-- Modal -->
<div class="modal fade" id="modalMap" tabindex="-1" role="dialog" aria-labelledby="modalMapLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Mapa</h4>
            </div>
            <div class="modal-body">
                <div id="mapContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div>
    <?php
    if(!empty($jefe)) {
        echo '<div class="well well-sm"><strong>Coordinado por:</strong></div>
            <div class="media">
                <div class="media-left">
                    <img class="media-object imgPerson img-rounded" src="'.$jefe['foto'].'">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">'.($jefe['APELLIDO_PATERNO'].' '.$jefe['APELLIDO_MATERNO'].' '.$jefe['NOMBRE']).'</h4>
                    <p><strong>Puesto:</strong> '.$jefe['puesto'].'. <strong>Domicilio:</strong> '.$jefe['CALLE'].
                    '. <strong>E-Mail:</strong> '.$dependiente['CORREOELECTRONICO'].'. <strong>Tel. Móvil:</strong> '.$dependiente['TELMOVIL'].'</p>';
                    $form = ActiveForm::begin([
                            'options' => ['class' => 'form-inline'],
                            'method' => 'POST',
                            'action' => Url::toRoute('padron/persona', true)
                        ]);
                    echo '<input type="hidden" name="id" id="id" value="'.$jefe['CLAVEUNICA'].'">
                    <button type="submit" class="btn btn-default">Ver</button>';
                    ActiveForm::end();
                    //<a href="'.Url::toRoute(['padron/persona', 'id' => $jefe['CLAVEUNICA']], true).'" class="btn btn-default">Ver</a>
                echo '</div>
            </div>
            <hr>';
    }
    ?>
    <?php
    if(!empty($dependientes)) {
        echo '<div class="well well-sm"><strong>Coordina a:</strong></div>';

        foreach ($dependientes as $dependiente) {
            echo '<div class="media">
                <div class="media-left">
                    <img class="media-object imgPerson img-rounded" src="'.$dependiente['foto'].'">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">'.($dependiente['APELLIDO_PATERNO'].' '.$dependiente['APELLIDO_MATERNO'].' '.$dependiente['NOMBRE']).'</h4>
                    <p><strong>Puesto:</strong> '.$dependiente['puesto'].'. <strong>Domicilio:</strong> '.$dependiente['CALLE'].
                    '. <strong>E-Mail:</strong> '.$dependiente['CORREOELECTRONICO'].'. <strong>Tel. Móvil:</strong> '.$dependiente['TELMOVIL'].'</p>';
                    $form = ActiveForm::begin([
                            'options' => ['class' => 'form-inline'],
                            'method' => 'POST',
                            'action' => Url::toRoute('padron/persona', true)
                        ]);
                    echo '<input type="hidden" name="id" id="id" value="'.$dependiente['CLAVEUNICA'].'">
                    <button type="submit" class="btn btn-default">Ver</button>';
                    ActiveForm::end();
                    //<a href="'.Url::toRoute(['padron/persona', 'id' => $dependiente['CLAVEUNICA']], true).'" class="btn btn-default">Ver</a>
                echo '</div>
            </div>
            <hr>';
        }
    }
    ?>
</div>

<div class="modal fade" id="modalListIntegrantes" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Lista de integrantes</h4>
            </div>
            <div class="modal-body">
                Hola mundo
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>