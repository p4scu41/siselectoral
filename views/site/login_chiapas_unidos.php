<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Iniciar Sesión';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-box" id="login-box">
    <div class="header_chiapas_solidario">
        <div class="row">
            <div class="col-xs-6 text-right">
                <img src="<?=Url::to("@web/img/chiapas_unidos_logo.png")?>" class="img-rounded" width="100" align="middle">
                <div style="border-left:2px solid #FFF;height:120px;float: right;margin-left: 10px;"></div>
            </div>
            <div class="col-xs-6 text-left">
                <h2>SIRECI</h2>
                <h3>Sistema de Red Ciudana</h3>
            </div>
        </div>
    </div>
    <div class="body_chiapas_solidario">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            //'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{input}{error}",
                //'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

        <?= $form->field($model, 'login')->textInput(['placeholder'=>'Usuario', 'autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder'=>'Contraseña', 'autocomplete'=>false]) ?>

        <div class="form-group">
            <div>
                <button type="submit" class="btn btn-block" style="background: #3B6437">Iniciar</button><br>
                <a href="#" class="pull-right" style="color: white"><strong>Recuperar Contrase&ntilde;a</strong></a>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="footer_chiapas_solidario">
    </div>

</div>