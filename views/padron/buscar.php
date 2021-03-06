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

    <div class="panel hidden-lg hidden-md hidden-sm">
        <div class="hidden-lg hidden-md hidden-sm col-xm-12">
            Si no logra ver toda la tabla deslice hacia la derecha <i class="fa fa-arrow-circle-right"></i>
        </div>
    </div>
    <!--Doble scroll http://jsfiddle.net/TBnqw/2096/-->
    <div id="scroller_wrapper">
        <div id="scroller"></div>
    </div>
    <div id="container_wrapper">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        //'filterModel' => $searchModel,
        //'layout' => "{summary}\n{items}\n{pager}",
        //'showHeader' => false,
        'options' => [
            'class' => 'grid-view responsive_table'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'NOMBRE',
            'APELLIDO_PATERNO',
            'APELLIDO_MATERNO',
            [
                'attribute' => 'MUNICIPIO',
                'value' => function ($model, $key, $index, $column) {
                    return $model->municipio->DescMunicipio;
                },
                //'filter' => $municipios,
            ],
            /*[
                'attribute' => 'SEXO',
                'value' => function ($model, $key, $index, $column) {
                    return $model->genero;
                },
                //'filter' => ['M'=>'Mujer', 'H'=>'Hombre'],
            ],*/
            [
                'attribute' => 'FECHANACIMIENTO',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->FECHANACIMIENTO) {
                        $fecha = new DateTime($model->FECHANACIMIENTO);
                        return $fecha->format('d-m-Y');
                    }
                    return '';
                },
            ],
            'SECCION',
            [
                'attribute' => 'DOMICILIO',
                'value' => function ($model, $key, $index, $column) {
                    return $model->DOMICILIO.
                        ($model->CODIGO_POSTAL ? ', C.P. '.$model->CODIGO_POSTAL: '')
                        .', '.$model->DES_LOC.' '.$model->NOM_LOC;
                },
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

</div>
