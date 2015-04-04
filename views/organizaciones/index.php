<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrganizacionesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Organizaciones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizaciones-index">
    <?php echo $this->render('_search', ['model' => $searchModel, 'municipios' => $municipios, 'tipos' => $tipos]); ?>

    <p>
        <?= Html::a('Crear nueva organización', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'Nombre',
            'Siglas',
            [
                'attribute' => 'IdPersonaRepresentante',
                'value' => function ($model, $key, $index, $column) {
                    return $model->representante->APELLIDO_PATERNO.' '.
                        $model->representante->APELLIDO_MATERNO.' '.
                        $model->representante->NOMBRE;
                }
            ],
            [
                'attribute' => 'IdPersonaEnlace',
                'value' => function ($model, $key, $index, $column) {
                    return $model->enlace->APELLIDO_PATERNO.' '.
                        $model->enlace->APELLIDO_MATERNO.' '.
                        $model->enlace->NOMBRE;
                }
            ],
            [
                'attribute' => 'IdMunicipio',
                'value' => function ($model, $key, $index, $column) {
                    return $model->municipio->DescMunicipio;
                }
            ],
            [
                'attribute' => 'idTipoOrganizacion',
                'value' => function ($model, $key, $index, $column) {
                    return $model->tipoOrganizacion->Descripcion;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Integrantes',
                'template' => '{integrantes}',
                'buttons' => [
                  'integrantes' => function ($url, $model, $key) {
                    return '<div class="text-center">'.Html::a('<i class="fa fa-users"></i>', Url::toRoute(['organizaciones/integrantes', 'idOrg' => $model->IdOrganizacion], true), [
                            'title' => 'Ver Integrantes',
                    ]).'</div>';
                  }
                ]
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
