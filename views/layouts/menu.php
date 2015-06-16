<?php

use yii\helpers\Url;
use app\helpers\PerfilUsuario;

?>

<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu">
    <li class="<?= (Yii::$app->request->getPathInfo() == '' || strpos(Yii::$app->request->getPathInfo(), 'site/index') != false) ? 'active' : '' ?>" >
        <a href="<?= Url::to(['site/index']) ?>">
            <i class="fa fa-dashboard"></i> <span>Inicio</span>
        </a>
    </li>
    <?PHP if (PerfilUsuario::hasPermiso('7cf41ceb-f4a5-4fd8-88c6-3f991348d250', 'R')) { ?>
    <li class="<?= Yii::$app->request->getPathInfo() == 'site/positiontree' ? 'active' : '' ?>">
        <a href="<?= Url::to(['site/positiontree']) ?>">
            <i class="fa fa-sitemap"></i> <span>Estrategia Municipal</span>
        </a>
    </li>
    <?PHP } ?>

    <?PHP if (PerfilUsuario::hasPermiso('a4cd8559-5d0d-43ba-bc7f-db91cc927a0f', 'R')) { ?>
    <li class="<?= Yii::$app->request->getPathInfo() == 'organizaciones/' ? 'active' : '' ?>">
        <a href="<?= Url::to(['organizaciones/index']) ?>">
            <i class="fa fa-list-alt"></i> <span>Organizaciones</span>
        </a>
    </li>
    <?PHP } ?>

    <?PHP if (PerfilUsuario::hasPermiso('36b78aa7-3642-489f-975a-a7213937af74', 'R')) { ?>
    <li class="treeview <?= stripos(Yii::$app->request->getPathInfo(), 'padron') !== false ? 'active' : '' ?>">
        <a href="#">
            <i class="fa fa-users"></i> <span>Persona</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" style="display: none;">
            <?PHP if (PerfilUsuario::hasPermiso('36b78aa7-3642-489f-975a-a7213937af74', 'F')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'padron/buscar') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['padron/buscar']) ?>" style="margin-left: 10px;"><i class="fa fa-search"></i> Buscar</a>
            </li>
            <?PHP } ?>

            <?PHP if (PerfilUsuario::hasPermiso('36b78aa7-3642-489f-975a-a7213937af74', 'C')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'padron/create') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['padron/create']) ?>" style="margin-left: 10px;"><i class="fa fa-user-plus"></i> Registrar</a>
            </li>
            <?PHP } ?>
        </ul>
    </li>
    <?PHP } ?>

    <?PHP if (PerfilUsuario::hasPermiso('ce7ad335-baee-498f-b4b0-94c283d9701b', 'R')) { ?>
    <li class="treeview <?= stripos(Yii::$app->request->getPathInfo(), 'promocion') !== false ? 'active' : '' ?>">
        <a href="#">
            <i class="fa fa-users"></i> <span>Promoción</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" style="display: none;">
            <?PHP if (PerfilUsuario::hasPermiso('ce7ad335-baee-498f-b4b0-94c283d9701b', 'F')) { ?>
            <li class="<?= Yii::$app->request->getPathInfo() == 'promocion/' ? 'active' : '' ?>">
                <a href="<?= Url::to(['promocion/index']) ?>" style="margin-left: 10px;"><i class="fa fa-search"></i> Buscar</a>
            </li>
            <?PHP } ?>
            <?PHP if (PerfilUsuario::hasPermiso('ce7ad335-baee-498f-b4b0-94c283d9701b', 'C')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'promocion/create') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['promocion/create']) ?>" style="margin-left: 10px;"><i class="fa fa-user-plus"></i> Promover</a>
            </li>
            <?PHP } ?>
        </ul>
    </li>
    <?PHP } ?>

    <?PHP if (PerfilUsuario::hasPermiso('b3d614ee-f96d-4f42-8c36-2a6e4b6eabeb', 'R') || PerfilUsuario::hasPermiso('5ea0a4c2-22f6-4fdc-9050-f25a65f3be91', 'R')) { ?>
    <li class="treeview <?= stripos(Yii::$app->request->getPathInfo(), 'reporte') !== false ? 'active' : '' ?>">
        <a href="#">
            <i class="fa fa-bar-chart"></i> <span>Reportes</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" style="display: none;">
            <?PHP if (PerfilUsuario::hasPermiso('b3d614ee-f96d-4f42-8c36-2a6e4b6eabeb', 'R')) { ?>
            <li class="<?= Yii::$app->request->getPathInfo() == 'reporte/' ? 'active' : '' ?>">
                <a href="<?= Url::to(['reporte/index']) ?>" style="margin-left: 10px;"><i class="fa fa-sitemap"></i> Estructura</a>
            </li>
            <?PHP } ?>
            <?PHP if (PerfilUsuario::hasPermiso('5ea0a4c2-22f6-4fdc-9050-f25a65f3be91', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'reporte/promovidos') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['reporte/promovidos']) ?>" style="margin-left: 10px;"><i class="fa fa-users"></i> Promovidos</a>
            </li>
            <?PHP } ?>
        </ul>
    </li>
    <?PHP } ?>

    <!--<li>
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
    </li>-->
</ul>