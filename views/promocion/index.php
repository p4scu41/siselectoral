<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PromocionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Promoción';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('urlGetOrganizaciones = "'.Url::toRoute('promocion/getorganizaciones').'";', \yii\web\View::POS_HEAD);
$this->registerJsFile(Url::to('@web/js/index_promocion.js'));
$this->registerJsFile(Url::to('@web/js/plugins/json-to-table.js'));
?>
<div class="promocion-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'IdEstructuraMov',
            [
                'attribute' => 'IdPersonaPromueve',
                'value' => function ($model, $key, $index, $column) {
                    return $model->personaPromueve->puesto->Descripcion. ' '. $model->personaPromueve->nombreCompleto;
                }
            ],
            [
                'attribute' => 'IdPuesto',
                'value' => function ($model, $key, $index, $column) {
                    return $model->puesto->Descripcion.' '.$model->personaPuesto->nombreCompleto;;
                }
            ],
            [
                'attribute' => 'IdpersonaPromovida',
                'value' => function ($model, $key, $index, $column) {
                    return $model->personaPromovida->nombreCompleto;
                }
            ],
            'FechaPromocion',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Organización',
                'template' => '<div class="text-center"> {organizacion} </div>',
                'buttons' => [
                    'organizacion'  => function ($url, $model, $key) {
                        return '<a class="btn btn-default btnVerOrganizacion" href="#" '.
                                'role="button" data-id="'.$model->IdpersonaPromovida.'" title="Ver Organizaciones">'.
                                $model->getCountOrganizaciones().'</i> '.
                            '</a>';
                    }
                ]
            ],

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>

<div class="modal fade" id="modalOrganizaciones" tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Organizaciones</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>