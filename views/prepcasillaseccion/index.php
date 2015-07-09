<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PREPCasillaSeccionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Casillas por Sección';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepcasilla-seccion-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Asignar Casilla a Sección', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id_casilla_seccion',
            [
                'header' => 'Municipio',
                'value' => function ($model, $key, $index, $column) {
                    return $model->seccion->cmunicipio->DescMunicipio;
                },
            ],
            [
                'attribute' => 'id_seccion',
                'value' => function ($model, $key, $index, $column) {
                    return $model->seccion->seccion;
                },
            ],
            [
                'attribute' => 'id_casilla',
                'value' => function ($model, $key, $index, $column) {
                    return $model->casilla->descripcion;
                },
            ],
            'descripcion',
            'colonia',
            //'domicilio',
            // 'cp',
            // 'localidad',
            // 'repre_gral',
            // 'tel_repre_gral',
            'repre_casilla',
            'tel_repre_casilla',
            // 'observaciones',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="text-center"> {view} &nbsp; {update} &nbsp; {delete} </div>'
            ]
        ],
    ]); ?>

</div>
