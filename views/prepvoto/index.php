<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
//use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PREPVotoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Registro de Votos';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Url::to('@web/js/prepvoto.js'));
$this->registerJsFile(Url::to('@web/js/plugins/jquery.numeric.js'));
$this->registerJs('urlVotar = "'.Url::toRoute('prepvoto/votar').'"', yii\web\View::POS_HEAD);
$this->registerJs('urlObservacion = "'.Url::toRoute('prepcasillaseccion/observacion').'"', yii\web\View::POS_HEAD);
$this->registerCssFile(Url::to('@web/css/voto.css'));
?>
<div class="prepvoto-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //echo Html::a('Create Prepvoto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php /*GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_partido',
            'id_casilla_seccion',
            'no_votos',
            'observaciones',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);*/ ?>

</div>

<div class="row">
    <!-- left column -->
    <div class="col-lg-12">

        <!-- general form elements -->
        <div class="box box-primary box-success">

            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Elección</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                                'options' => ['class' => 'form-inline'],
                                'id' => 'formFiltroVotos',
                            ]); ?>
                        <div id="bodyForm">
                            <div class="form-group">
                                <label for="tipoEleccion">Tipo de Elección: </label>
                                <?= Html::dropDownList('tipoEleccion', Yii::$app->request->post('tipoEleccion'), $tiposEleccion, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'tipoEleccion']); ?>
                            </div>
                            <div class="form-group <?= Yii::$app->request->post('municipio')!=null ? '' : 'hidden' ?>">
                                <label for="municipio">Municipio: </label>
                                <?= Html::dropDownList('municipio', Yii::$app->request->post('municipio'), $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'municipio']); ?>
                            </div>
                            <div class="form-group <?= Yii::$app->request->post('distritoLocal')!=null ? '' : 'hidden' ?>">
                                <label for="distritoLocal">Distrito Local: </label>
                                <?= Html::dropDownList('distritoLocal', Yii::$app->request->post('distritoLocal'), $distritosLocales, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'distritoLocal']); ?>
                            </div>
                            <div class="form-group <?= Yii::$app->request->post('distritoFederal')!=null ? '' : 'hidden' ?>">
                                <label for="distritoFederal">Distrito Federal: </label>
                                <?= Html::dropDownList('distritoFederal', Yii::$app->request->post('distritoFederal'), $distritosFederales, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'distritoFederal']); ?>
                            </div>
                        </div>
                        <div id="alertResult"></div>
                        <p>
                            <button type="button" class="btn btn-success" id="btnAceptar">
                                <i class="fa fa-building-o"></i> Aceptar
                            </button>
                            <i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;" id="loadIndicator"></i>
                        </p>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div><!-- /.box -->

    </div><!--/.col (left) -->

    <div class="col-lg-12">
        <?php
        $tabla = '';

        if (!empty($casillas)) {
            $tabla = '<table class="table table-condensed table-striped table-bordered table-hover" id="panelVotos">';
            $thead = '<thead><tr>'
                . '<th></th>';
            $sumaCandidatos = [];

            foreach ($candidatos as $candidato) {
                $sumaCandidatos[$candidato->id_candidato] = 0;
                $thead .= '<th><img src="'.$candidato->partido->logo.'" class="logoPartido" data-toggle="tooltip" data-placement="top" title="'.$candidato->nombre.'"></th>';
            }

            $thead .= '<th>Total</th>'
                . '<th>Observaciones</th>'
                . '</tr>';
            
            $tbody = '<tbody>';

            foreach ($casillas as $casilla) {
                $tbody .= '<tr class="text-center">';
                $tbody .= '<td>Sección '.$casilla['seccion'].' - '.$casilla['descripcion'].'</td>';
                $sumaCasilla = 0;

                foreach ($candidatos as $candidato) {
                    $tbody .= '<td><input type="number" id="'.$candidato->id_candidato.'-'.$casilla['id_casilla_seccion'].'" '
                        . 'name="'.$candidato->id_candidato.'-'.$casilla['id_casilla_seccion'].'" '
                        . 'value="'.$votos[$candidato->id_candidato][$casilla['id_casilla_seccion']].'" min="0" class="inputVoto"></td>';
                    $sumaCasilla += $votos[$candidato->id_candidato][$casilla['id_casilla_seccion']];
                    $sumaCandidatos[$candidato->id_candidato] += $votos[$candidato->id_candidato][$casilla['id_casilla_seccion']];
                }
                
                $tbody .= '<td class="sumaCasilla-'.$casilla['id_casilla_seccion'].'">'.$sumaCasilla.'</td>
                    <td><input type="text" id="observacion-'.$casilla['id_casilla_seccion'].'" name="observacion-'.$casilla['id_casilla_seccion'].'" value="'.$casilla['observaciones'].'" class="inputObservacion" maxlength="200"></td>
                </tr>';
            }

            $sumaTotal = 0;
            $thead .= '<tr><th>Votos</th>';

            // Sumas
            foreach ($candidatos as $candidato) {
                $sumaTotal += $sumaCandidatos[$candidato->id_candidato];
                $thead .= '<th class="sumaCandidato-'.$candidato->id_candidato.'">'.$sumaCandidatos[$candidato->id_candidato].'</th>';
            }

            $thead .= '<th class="totalVotos">'.$sumaTotal.'</th>'
                . '<th></th>'
                . '</tr><tr><th>Porcentajes</th>';

            // Porcentajes
            foreach ($candidatos as $candidato) {
                $thead .= '<th class="porcentaje-'.$candidato->id_candidato.'">'.($sumaTotal != 0 ? round($sumaCandidatos[$candidato->id_candidato]/$sumaTotal,2)*100 : 0).' %</th>';
            }

            $thead .= '<th>100 %</th>'
                . '<th></th>'
                . '</tr></thead>';

            $tbody .= '</tbody>';

            $tabla .= $thead.$tbody.'</table>';
        }

        echo $tabla;

        if (empty($casillas) && Yii::$app->request->post('tipoEleccion') != null) {
            echo '<div class="alert alert-danger">No se econtraron datos que mostrar</div>';
        }
        ?>
    </div>
</div>   <!-- /.row -->