<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PREPPartidoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $titulo_plu;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="preppartido-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Registrar', ['create'], ['class' => 'btn btn-success', 'id' => 'btnCreate']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'nombre',
            'nombre_corto',
            'propietario',
            // 'observaciones',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="text-center"> {view} &nbsp; {update} &nbsp; {delete} </div>'
            ]
        ],
    ]); ?>

</div>
