<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PromocionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Promoción';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promocion-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p><?= Html::a('Registrar', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'IdEstructuraMov',
            [
                'attribute' => 'IdPersonaPromueve',
                'value' => function ($model, $key, $index, $column) {
                    return $model->personaPromueve->nombreCompleto;
                }
            ],
            [
                'attribute' => 'IdPuesto',
                'value' => function ($model, $key, $index, $column) {
                    return $model->puesto->Descripcion;
                }
            ],
            [
                'attribute' => 'IdPersonaPuesto',
                'value' => function ($model, $key, $index, $column) {
                    return $model->personaPuesto->nombreCompleto;
                }
            ],
            [
                'attribute' => 'IdpersonaPromovida',
                'value' => function ($model, $key, $index, $column) {
                    return $model->personaPromovida->nombreCompleto;
                }
            ],
            'FechaPromocion',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
