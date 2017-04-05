<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PREPCandidatoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $titulo_plu;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepcandidato-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Registrar', ['create'], ['class' => 'btn btn-success', 'id' => 'btnCreate']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id_partido',
                'value' => function ($model, $key, $index, $column) {
                    return $model->partido->nombre_corto;
                },
            ],
            'nombre',
            [
                'attribute' => 'id_tipo_eleccion',
                'value' => function($model, $key, $index, $column) {
                    return $model->tipoEleccion->descripcion;
                }
            ],
            [
                'header' => 'Ubicación',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->cmunicipio) {
                        return $model->cmunicipio->DescMunicipio;
                    }

                    if ($model->distrito_local) {
                        return $model->distrito_local;
                    }

                    if ($model->distrito_federal) {
                        return $model->distrito_federal;
                    }
                },
            ],
            //'distrito_local',
            // 'distrito_federal',
            'activo:boolean',
            'orden',
            // 'observaciones',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="text-center"> {view} &nbsp; {update} &nbsp; {delete} </div>'
            ]
        ],
    ]); ?>

</div>
