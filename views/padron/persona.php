<?php

use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
$this->title = 'Detalles estructura';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Persona';
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
                        <div class="col-md-4 text-center">
                            <div>
                                <img src="<?= $persona->foto ?>" class="img-rounded imgPerson" id="imgPerson">
                                <h4 id="nombreCompleto"><?= ($persona->APELLIDO_PATERNO.' '.$persona->APELLIDO_MATERNO.' '.$persona->NOMBRE); ?></h4>
                            </div>
                            <div>
                                <span class="btn btn-app btn-sm btn-yellow">
                                    <span class="line-height-1 bigger-170"><?= (int)$numDepend['cantidad'] ?></span>
                                    <br>
                                    <span class="puesto"><?= ($numDepend['puesto'] != '' ? $numDepend['puesto'] : 'Sin Dependencias') ?></span>
                                </span>
                                <span class="btn btn-app btn-sm btn-pink">
                                    <span class="line-height-1 bigger-170"> 4 </span>
                                    <br>
                                    <span class=""> Programas </span>
                                </span>
                                <span class="btn btn-app btn-sm btn-grey">
                                    <span class="line-height-1 bigger-170"> 1 </span>
                                    <br>
                                    <span class=""> Organización </span>
                                </span>
                                <span class="btn btn-app btn-sm btn-success">
                                    <span class="line-height-1 bigger-170"> 98% </span>
                                    <br>
                                    <span class=""> Meta </span>
                                </span>
                            </div>
                            <br>
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

<div class="text-center">
    <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modalMap">
        <span class="glyphicon glyphicon-globe"></span>
        <span class="fa fa-map-marker"></span> Ver Mapa
    </button>
</div>
<br>

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
                    '. <strong>E-Mail:</strong> '.$dependiente['CORREOELECTRONICO'].'. <strong>Tel. Móvil:</strong> '.$dependiente['TELMOVIL'].'</p>
                    <a href="'.Url::toRoute(['padron/persona', 'id' => $jefe['CLAVEUNICA']], true).'" class="btn btn-default">Ver</a>
                </div>
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
                    '. <strong>E-Mail:</strong> '.$dependiente['CORREOELECTRONICO'].'. <strong>Tel. Móvil:</strong> '.$dependiente['TELMOVIL'].'</p>
                    <a href="'.Url::toRoute(['padron/persona', 'id' => $dependiente['CLAVEUNICA']], true).'" class="btn btn-default">Ver</a>
                </div>
            </div>
            <hr>';
        }
    }
    ?>
</div>