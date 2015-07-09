<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PREPSeccionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $titulo_plu;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepseccion-index">

    <?php echo $this->render('_search', ['model' => $searchModel, 'municipios' => $municipios]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Registrar', ['create'], ['class' => 'btn btn-success', 'id' => 'btnCreate']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id_seccion',
            [
                'attribute' => 'municipio',
                'value' => function ($model, $key, $index, $column) {
                    return $model->cmunicipio->DescMunicipio;
                },
            ],
            'zona',
            'seccion',
            //'distrito_local',
            // 'distrito_federal',
            // 'observaciones',
            'activo:boolean',
            // 'fecha_cierre',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="text-center"> &nbsp; {update} &nbsp; </div>'
            ]
        ],
    ]); ?>

</div>
