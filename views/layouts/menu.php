<?php

use yii\helpers\Url;
?>

<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu">
    <li class="<?= (Yii::$app->request->getPathInfo() == '' || strpos(Yii::$app->request->getPathInfo(), 'site/index') != false) ? 'active' : '' ?>" >
        <a href="<?= Url::to(['site/index']) ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
    </li>
    <li class="<?= Yii::$app->request->getPathInfo() == 'site/positiontree' ? 'active' : '' ?>">
        <a href="<?= Url::to(['site/positiontree']) ?>">
            <i class="fa fa-sitemap"></i> <span>Estructura</span>
        </a>
    </li>
    <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'padron') !== false ? 'active' : '' ?>">
        <a href="#">
            <i class="fa fa-users"></i> <span>Padron</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-bar-chart"></i> <span>Reportes</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-cog"></i> <span>Configuraciones</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-envelope-o"></i> <span>Notificaciones</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-question-circle"></i> <span>Ayuda</span>
        </a>
    </li>
</ul>