<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\PadronGlobal */

$this->title = 'Detalles de la persona';
$this->params['breadcrumbs'][] = 'Persona';
$this->params['breadcrumbs'][] = 'Detalles';

$this->registerJsFile('http://maps.google.com/maps/api/js?sensor=false');
$this->registerJsFile(Url::to('@web/js/persona.js'));
?>
<div class="row">
    <!-- left column -->
    <div class="col-lg-12">
        <!-- general form elements -->
        <div class="box box-primary box-success">

            <div class="panel panel-success" id="containerPerson">
                <div class="panel-heading">
                    <h3 class="panel-title inline">Datos Personales</h3>
                    <a href="#" class="btn btn-success pull-right btn-sm backBtn"><i class="fa fa-mail-reply"></i> Regresar</a>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <img src="<?= $model->getFoto() ?>" class="img-rounded imgPerson" id="imgPerson">
                                <h4 id="nombreCompleto"><?= ($model->APELLIDO_PATERNO.' '.$model->APELLIDO_MATERNO.' '.$model->NOMBRE); ?></h4>
                            </div>
                            <form class="form-horizontal" method="POST" id="frmPersonDetails">
                                <div class="form-group">
                                    <label class="col-sm-2 col-md-4 control-label">E-mail</label>
                                    <div class="col-sm-10 col-md-8">
                                        <div class="well well-sm"><?= ($model->CORREOELECTRONICO) ? $model->CORREOELECTRONICO : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-md-4 control-label">Tel. Casa</label>
                                    <div class="col-sm-10 col-md-8">
                                        <div class="well well-sm"><?= ($model->TELCASA) ? $model->TELCASA : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-md-4 control-label">Tel. Móvil</label>
                                    <div class="col-sm-10 col-md-8">
                                        <div class="well well-sm"><?= ($model->TELMOVIL) ? $model->TELMOVIL : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-md-4 control-label">Fecha Nacimiento</label>
                                    <div class="col-sm-10 col-md-8">
                                        <div class="well well-sm"><?= ($model->fechaNac) ? $model->fechaNac : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-md-4 control-label">Edad</label>
                                    <div class="col-sm-10 col-md-8">
                                        <div class="well well-sm"><?= ($model->edad) ? $model->edad : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="col-md-8">
                            <form class="form-horizontal" method="POST" id="frmPersonDetails">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Domicilio</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= $model->DOMICILIO.', C.P. '.$model->DES_LOC.', '.$model->NOM_LOC; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Estado Civil</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Zona</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Sección</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($model->SECCION) ? $model->SECCION : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Género</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($model->SEXO) ? $model->genero : '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Distrito</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm"><?= ($model->DISTRITO) ? $model->DISTRITO: '&nbsp;'; ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Ocupación</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Escolaridad</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Observaciones</label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm">&nbsp;</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="<?= Url::toRoute(['padron/update', 'id'=>$model->CLAVEUNICA], true); ?>" class="btn btn-app btn-success btn-sm">
                                <span class="fa fa-pencil-square-o"></span> Editar
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->
</div>   <!-- /.row -->

<div class="text-center">
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalMapDomicilio">
        <span class="glyphicon glyphicon-globe"></span>
        <span class="fa fa-map-marker"></span> Ubicar domicilio
    </button>

    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalMapCasilla">
        <span class="glyphicon glyphicon-globe"></span>
        <span class="fa fa-map-marker"></span> Ubicar casilla
    </button>
</div>
<br>

<!-- Modal -->
<div class="modal fade" id="modalMapDomicilio" tabindex="-1" role="dialog" aria-labelledby="modalMapLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Domicilio</h4>
            </div>
            <div class="modal-body">
                <div id="mapContainerDomicilio" class="mapContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMapCasilla" tabindex="-1" role="dialog" aria-labelledby="modalMapLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Casilla</h4>
            </div>
            <div class="modal-body">
                <div id="mapContainerCasilla" class="mapContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
