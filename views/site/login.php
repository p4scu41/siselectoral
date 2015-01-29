<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-box" id="login-box">
    <div class="header">Iniciar Sesi&oacute;n</div>
    <form action="<?= Url::to(['site/index']) ?>" method="post">
        <div class="body bg-gray">
            <div class="form-group">
                <input type="text" name="user" class="form-control" placeholder="Usuario"/>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Contraseña"/>
            </div>
        </div>
        <div class="footer">
            <button type="submit" class="btn bg-olive btn-block">Iniciar</button>

            <p><a href="#">Recuperar Contrase&ntilde;a</a></p>

        </div>
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
    </form>

</div>