<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PadronGlobalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Buscar';
$this->params['breadcrumbs'][] = 'Persona';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="padron-global-index">

    <?php echo $this->render('_search', ['model' => $searchModel, 'municipios' => $municipios]); ?>

    <?php if (!empty(Yii::$app->request->get())) { ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'grid-view table-responsive'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'APELLIDO_PATERNO',
            'APELLIDO_MATERNO',
            'NOMBRE',
            [
                'attribute' => 'MUNICIPIO',
                'value' => function ($model, $key, $index, $column){
                    return $model->municipio->DescMunicipio;
                },
                'filter' => $municipios,
            ],
            [
                'attribute' => 'SEXO',
                'value' => function ($model, $key, $index, $column){
                    return $model->genero;
                },
                'filter' => ['M'=>'Mujer', 'H'=>'Hombre'],
            ],
            ['class' => 'yii\grid\ActionColumn',
              'template'=>'{ver}',
                'buttons'=>[
                  'ver' => function ($url, $model) {
                    return Html::a('<i class="fa fa-'.($model->SEXO=='H' ? 'male' : 'female').' fa-2x"></i>', Url::toRoute(['padron/view', 'id' => $model->CLAVEUNICA], true), [
                            'title' => 'Ver detalles',
                    ]);
                  }
                ]
            ],
        ],
    ]); ?>
    <?php } ?>

</div>
