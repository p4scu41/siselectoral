<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Organizaciones */

$this->title = 'Organización: ' . $model->Nombre;
$this->params['breadcrumbs'][] = ['label' => 'Organizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Url::to('@web/js/integrantes.js'));
$this->registerJs('var delIntegrante = "'.Url::toRoute('organizaciones/delintegrante', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('var addIntegrante = "'.Url::toRoute('organizaciones/addintegrante', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('var idOrg = '.$model->IdOrganizacion.';', \yii\web\View::POS_HEAD);
?>
<div class="organizaciones-view">

    <div>
        <?php $numIntegrantes = count($integrantes); ?>
        Total de integrantes: <strong><?= $numIntegrantes ?></strong> &nbsp;
        <a href="#" class="btn btn-success" id="btnAddIntegrante">Agregar nuevo integrante</a> &nbsp; 
        <a href="<?= Url::toRoute('organizaciones/index', true) ?>" class="btn btn-default">Regrear al listado de organizaciones</a>
        <br><br>
        <p>
            Filtrar por sección: &nbsp; 
            <?= Html::dropDownList('secciones', null, [0=>'Seleccione una opción'] + $secciones,
                ['class'=>'form-control inline', 'id'=>'secciones']); ?> &nbsp;
            <span id="noIntegrantesSeccion"></span>
        </p>
    </div><br>
    <div class="table-responsive" id="resultTblIntegrantes">
        <table id="tblIntegrantes" class="table table-condensed table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Sección</th>
                    <th class="text-center">Dirección</th>
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
                        . '<td class="text-center"><button class="btn btn-sm btn-danger btnDelIntegrante" '.
                            'data-id="'.$integrantes[$count]['CLAVEUNICA'].'" '.
                            '><i class="fa fa-user-times"></i></button></td>'
                    . '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php echo $this->render('_frmBuscarPersona', ['municipios'=>$municipios]) ?>

</div>
