<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Organizaciones */

$this->title = 'Organización: ' . $model->Nombre;
$this->params['breadcrumbs'][] = ['label' => 'Organizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Url::to('@web/js/integrantes.js'), ['depends' => [\kartik\select2\Select2Asset::className()]]);
$this->registerJsFile(Url::to('@web/js/plugins/table2CSV.js'));
$this->registerJs('var delIntegrante = "'.Url::toRoute('organizaciones/delintegrante', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('var addIntegrante = "'.Url::toRoute('organizaciones/addintegrante', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('var idOrg = '.$model->IdOrganizacion.';', \yii\web\View::POS_HEAD);
$this->registerJs('var getPromotores = "'.Url::toRoute('organizaciones/getpromotorintegrante', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('var getOtrasOrgs = "'.Url::toRoute('organizaciones/getotrasorgs', true).'";', \yii\web\View::POS_HEAD);
?>
<div class="organizaciones-view">

    <div>
        <?php $numIntegrantes = count($integrantes); ?>
        Total de integrantes: <strong><?= $numIntegrantes ?></strong> &nbsp;
        <a href="#" class="btn btn-success" id="btnAddIntegrante">Agregar nuevo integrante</a> &nbsp; 
        <a href="<?= Url::toRoute('organizaciones/index', true) ?>" class="btn btn-default">Regresar al listado de organizaciones</a>
        <br><br>
        <div class="row">
            <div class="col-md-1">
            Filtrar por sección: &nbsp;
            </div>
            <div class="col-md-9">
            <?php
            echo Select2::widget([
                'name' => 'secciones',
                'options' => ['placeholder' => 'Seleccione las secciones', 'id'=>'secciones', 'multiple' => true],
                'data' => $secciones
            ]);
            ?>
            </div>
            <div class="col-md-2" id="noIntegrantesSeccion"></div>
        </div>
    </div><br>
    <div class="opcionesExportar" style="display: none;">
        <a href="#" data-url="<?= Url::to(['reporte/pdf'], true) ?>" class="btn btn-default btnExportPdf">
            <i class="fa fa-file-pdf-o"></i> Exportar a pdf
        </a>
        <!--<a href="#" data-url="<?= Url::to(['reporte/excel'], true) ?>" class="btn btn-default btnExportExcel">
            <i class="fa fa-file-excel-o"></i> Exportar a Excel
        </a>-->
    </div>
    <h3 class="text-center" id="titulo">Integrantes de la organización</h3>
    <div class="table-responsive" id="resultTblIntegrantes">
        <table id="tblIntegrantes" class="table table-condensed table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Sección</th>
                    <th class="text-center">Dirección</th>
                    <th class="text-center">Promovido</th>
                    <th class="text-center">Otras Organizaciones</th>
                    <th class="text-center">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                for ($count=0; $count<$numIntegrantes; $count++) {
                    echo '<tr>'
                        . '<td>' . $integrantes[$count]['NombreCompleto'] . '</td>'
                        . '<td class="seccion"> ' . (int)$integrantes[$count]['SECCION'] . ' </td>'
                        . '<td>' . $integrantes[$count]['Domicilio'] . '</td>'
                        . '<td class="text-center"><a href="#" class="promovidoIntegrante" data-id="'.$integrantes[$count]['CLAVEUNICA'].'" data-promotor="'.$integrantes[$count]['IdPErsonaPromueve'].'"><i class="fa fa-' . ($integrantes[$count]['IdPErsonaPromueve']==null ? '' : 'check-') . 'square-o fa-lg"></i></a></td>'
                        . '<td class="text-center"><a class="btn btn-default btnVerOtrasOrganizacion" href="#" '.
                            'role="button" data-id="'.$integrantes[$count]['CLAVEUNICA'].'" data-org="'.((int)$_GET['idOrg']).'" title="Ver Otras Organizaciones"> '.$integrantes[$count]['otrasOrganizaciones'].' </i> '
                        . '</a></td>'
                        . '<td class="text-center"><button class="btn btn-sm btn-danger btnDelIntegrante" '.
                            'data-id="'.$integrantes[$count]['CLAVEUNICA'].'" '.
                            '><i class="fa fa-user-times"></i></button></td>'
                    . '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="opcionesExportar" style="display: none;">
        <a href="#" data-url="<?= Url::to(['reporte/pdf'], true) ?>" class="btn btn-default btnExportPdf">
            <i class="fa fa-file-pdf-o"></i> Exportar a pdf
        </a>
        <!--<a href="#" data-url="<?= Url::to(['reporte/excel'], true) ?>" class="btn btn-default btnExportExcel">
            <i class="fa fa-file-excel-o"></i> Exportar a Excel
        </a>-->
    </div>

    <?php echo $this->render('_frmBuscarPersona', ['municipios'=>$municipios]) ?>

    <div id="formExport" style="display: none;"></div>

    <div id="dialog" title="Promoción del integrante seleccionado"></div>

</div>

<div class="modal fade" id="modalOtrasOrganizaciones" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Otras Organizaciones</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
