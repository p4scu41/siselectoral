<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PREPCasillaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $titulo_plu;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prepcasilla-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Registrar', ['create'], ['class' => 'btn btn-success', 'id' => 'btnCreate']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'descripcion',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="text-center"> {update} </div>'
            ]
        ],
    ]); ?>

</div>
