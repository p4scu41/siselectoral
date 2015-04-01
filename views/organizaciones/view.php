<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Organizaciones */

$this->title = 'Organización: ' . $model->Nombre;
$this->params['breadcrumbs'][] = ['label' => 'Organizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizaciones-view">

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->IdOrganizacion], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->IdOrganizacion], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Esta seguro que desea eliminar este registro?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Regresar al listado', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Nombre',
            'Siglas',
            [
                'attribute' => 'IdPersonaRepresentante',
                'value' => $model->representante->APELLIDO_PATERNO.' '.
                    $model->representante->APELLIDO_MATERNO.' '.
                    $model->representante->NOMBRE
            ],
            [
                'attribute' => 'IdPersonaEnlace',
                'value' => $model->enlace->APELLIDO_PATERNO.' '.
                    $model->enlace->APELLIDO_MATERNO.' '.
                    $model->enlace->NOMBRE
            ],
            [
                'attribute' => 'IdMunicipio',
                'value' => $model->municipio->DescMunicipio
            ],
            [
                'attribute' => 'idTipoOrganizacion',
                'value' => $model->tipoOrganizacion->Descripcion
            ],
            'Observaciones',
        ],
    ]) ?>

</div>
