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
    <div class="header">Iniciar Sesi&oacute;n</div>
    <div class="body bg-gray">
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
                <button type="submit" class="btn bg-olive btn-block">Iniciar</button><br>
                <a href="#" class="pull-right">Recuperar Contrase&ntilde;a</a>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="footer">
    </div>

</div>