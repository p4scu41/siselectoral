<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-index">

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Registrar', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'IdPersona',
                'value' => function ($model) {
                    return $model->persona->nombreCompleto;
                }
            ],
            [
                'attribute' => 'IdPerfil',
                'value' => function ($model) {
                    return $model->perfil->Nombre;
                }
            ],
            'login',
            // 'password',
            'Estado:boolean',
            // 'usrActualiza',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
