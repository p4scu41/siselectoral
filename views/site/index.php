<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'SIRECI';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(\yii\helpers\Url::to('@web/js/AdminLTE/dashboard.js'), ['position' => \yii\web\View::POS_END]);
$this->registerJs('urlResumen="'.Url::toRoute('site/getresumen', true).'";', \yii\web\View::POS_HEAD);
$this->registerJs('urlPositionTree="'.Url::toRoute('site/positiontree', true).'";', \yii\web\View::POS_HEAD);

$selectMunicipio = Yii::$app->user->identity->persona->MUNICIPIO ? Yii::$app->user->identity->persona->MUNICIPIO : Yii::$app->request->post('Municipio');
$readonly = Yii::$app->user->identity->persona->MUNICIPIO ? false : true;

$form = ActiveForm::begin([
        'options' => ['class' => 'form-inline'],
        'id' => 'formMunicipio',
    ]);
    echo Html::label('Municipio'). ' &nbsp; '
    . Html::dropDownList('Municipio', $selectMunicipio,
        $municipios, ['prompt' => 'Elija una opción', 'class' => 'form-control', 'id' => 'municipio', 'disabled'=>$readonly])
    . ' &nbsp; <button type="button" class="btn btn-primary" id="btnVerResumen"><i class="fa fa-newspaper-o"></i> Ver resumen</button>'
    . ' &nbsp; <button type="button" class="btn btn-primary" id="btnVerEstructura"><i class="fa fa-sitemap"></i> Ver estructura</button>';
ActiveForm::end();
?>
<br />
<!-- Small boxes (Stat box) -->
<div class="row" id="indicadoresMunicipio">
    
</div><!-- /.row -->
