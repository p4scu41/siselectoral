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

    <div class="text-center">
        <div class="btn btn-default">
            Total: Organizaciones: <strong> <?= $countOrganizaciones?> </strong>, 
                Beneficiarios: <strong> <?= number_format($totalBeneficiarios) ?> </strong>, 
                Promovidos: <strong><?= number_format($totalPromovidos) ?></strong>, 
                Duplicados: <strong><?= number_format($totalDuplicados) ?></strong>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'layout' => '{items} {summary} {pager}',
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'Nombre',
            'Siglas',
            [
                'attribute' => 'IdPersonaRepresentante',
                'value' => function ($model, $key, $index, $column) {
                    if (isset($model->representante)) {
                        return $model->representante->nombreCompleto;
                    }

                    return '';
                }
            ],
            [
                'attribute' => 'IdPersonaEnlace',
                'value' => function ($model, $key, $index, $column) {
                    if (isset($model->enlace)) {
                        return $model->enlace->nombreCompleto;
                    }

                    return '';
                }
            ],
            /*[
                'attribute' => 'IdMunicipio',
                'value' => function ($model, $key, $index, $column) {
                    return $model->municipio->DescMunicipio;
                }
            ],*/
            [
                'attribute' => 'idTipoOrganizacion',
                'value' => function ($model, $key, $index, $column) {
                    return $model->tipoOrganizacion->Descripcion;
                }
            ],
            [
                'header' => 'Beneficiarios',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    return '<div class="text-center">'.$model->getTotalIntegrantes().'</div>';
                }
            ],
            [
                'header' => 'Promovidos',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    return '<div class="text-center">'.$model->getTotalIntegrantesPromovidos().'</div>';
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
