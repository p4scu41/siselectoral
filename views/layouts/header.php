<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<!-- header logo: style can be found in header.less -->
<header class="header">
    <a href="<?= Url::home(); ?>" class="logo <?= Yii::$app->params['chiapas_unidos'] ? 'headerBorderBottom' : '' ?>">
        <!-- Add the class icon to your logo image or logo icon to add the margining -->
        SIRECI <br><span>Sistema de Red Ciudadana</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top <?= Yii::$app->params['chiapas_unidos'] ? 'headerBorderBottom' : '' ?>" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-warning"></i>
                        <span class="label label-warning">10</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Notificaciones</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li>
                                    <a href="#">
                                        <i class="ion ion-person-add info"></i> 5 nuevos coordinadores de promotor
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-warning danger"></i> No se alcanz&oacute; la meta de promovidos
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users warning"></i> Nuevo coordinador regional en la Costa
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="ion ion-help success"></i> Revisar metas en Tuxtla
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-sitemap danger"></i> Cambios en la estructura de Teopisca
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="#">Ver Todos</a></li>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span><?= Yii::$app->user->identity->login ?> <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-light-blue">
                            <img src="<?= Yii::$app->params['chiapas_unidos'] ? Url::to("@web/img/chiapas_unidos_logo.png") : Yii::$app->user->identity->persona->getFoto() ?>" class="img-rounded" />
                            <p>
                                <?= Yii::$app->user->identity->login ?>
                                <small>&Uacute;ltimo acceso: Hoy</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">Configuraciones</a>
                            </div>
                            <div class="pull-right">
                                <a href="<?= Url::to(['site/logout']) ?>" data-method="post" class="btn btn-default btn-flat">Salir</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>