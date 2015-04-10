<?php

use yii\helpers\Url;

?>

<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu">
    <li class="<?= (Yii::$app->request->getPathInfo() == '' || strpos(Yii::$app->request->getPathInfo(), 'site/index') != false) ? 'active' : '' ?>" >
        <a href="<?= Url::to(['site/index']) ?>">
            <i class="fa fa-dashboard"></i> <span>Inicio</span>
        </a>
    </li>
    <li class="<?= Yii::$app->request->getPathInfo() == 'site/positiontree' ? 'active' : '' ?>">
        <a href="<?= Url::to(['site/positiontree']) ?>">
            <i class="fa fa-sitemap"></i> <span>Estrategia Municipal</span>
        </a>
    </li>
    <li class="<?= Yii::$app->request->getPathInfo() == 'organizaciones/' ? 'active' : '' ?>">
        <a href="<?= Url::to(['organizaciones/index']) ?>">
            <i class="fa fa-list-alt"></i> <span>Organizaciones</span>
        </a>
    </li>
    <li class="treeview <?= stripos(Yii::$app->request->getPathInfo(), 'padron') !== false ? 'active' : '' ?>">
        <a href="#">
            <i class="fa fa-users"></i> <span>Persona</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" style="display: none;">
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'padron/buscar') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['padron/buscar']) ?>" style="margin-left: 10px;"><i class="fa fa-search"></i> Buscar</a>
            </li>
            <?php if (strtolower(Yii::$app->user->identity->perfil->IdPerfil) == strtolower(Yii::$app->params['idAdmin'])) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'padron/create') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['padron/create']) ?>" style="margin-left: 10px;"><i class="fa fa-user-plus"></i> Registrar</a>
            </li>
            <?php } ?>
        </ul>
    </li>
    <li class="<?= Yii::$app->request->getPathInfo() == 'promocion/' ? 'active' : '' ?>">
        <a href="<?= Url::to(['promocion/index']) ?>">
            <i class="fa fa-child"></i> <span>Promoción</span>
        </a>
    </li>
    <li class="treeview <?= stripos(Yii::$app->request->getPathInfo(), 'reporte') !== false ? 'active' : '' ?>">
        <a href="#">
            <i class="fa fa-bar-chart"></i> <span>Reportes</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" style="display: none;">
            <li class="<?= Yii::$app->request->getPathInfo() == 'reporte/' ? 'active' : '' ?>">
                <a href="<?= Url::to(['reporte/index']) ?>" style="margin-left: 10px;"><i class="fa fa-sitemap"></i> Estructura</a>
            </li>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'reporte/promovidos') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['reporte/promovidos']) ?>" style="margin-left: 10px;"><i class="fa fa-users"></i> Promovidos</a>
            </li>
        </ul>
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