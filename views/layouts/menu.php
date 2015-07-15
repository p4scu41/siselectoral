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
            <?PHP if (PerfilUsuario::hasPermiso('b3d614ee-f96d-4f42-8c36-2a6e4b6eabeb', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'reporte/seccional') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['reporte/seccional']) ?>" style="margin-left: 10px;"><i class="fa fa-cubes"></i> Seccional</a>
            </li>
            <?PHP } ?>
        </ul>
    </li>
    <?PHP } ?>

    <?PHP if (PerfilUsuario::hasPermiso('3e3d98fb-a3d2-4d4f-a63a-c010498891e0', 'R')) { ?>
    <li class="<?= Yii::$app->request->getPathInfo() == 'bingo/' ? 'active' : '' ?>">
        <a href="<?= Url::to(['bingo/index']) ?>">
            <i class="fa fa-building-o"></i> <span>Bingo</span>
        </a>
    </li>
    <?PHP } ?>

    <?PHP if (PerfilUsuario::hasPermiso('1fdee8d8-ef29-4966-badf-3a796b0e1570', 'R') || PerfilUsuario::hasPermiso('7b8b4d91-2b1d-4a65-9f29-a4c2f22c9b8f', 'R') || PerfilUsuario::hasPermiso('19cff826-d301-48bd-a824-960c67b7d6f6', 'R')) { ?>
    <li class="treeview <?= stripos(Yii::$app->request->getPathInfo(), 'prep') !== false ? 'active' : '' ?>">
        <a href="#">
            <i class="fa fa-info-circle"></i> <span>PREP</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" style="display: none;">
            <?PHP if (PerfilUsuario::hasPermiso('1fdee8d8-ef29-4966-badf-3a796b0e1570', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'prepcasilla/') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['prepcasilla/index']) ?>" style="margin-left: 10px;"><i class="fa fa-home"></i> Casillas</a>
            </li>
            <?PHP } ?>
            <?PHP if (PerfilUsuario::hasPermiso('1fdee8d8-ef29-4966-badf-3a796b0e1570', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'prepseccion/') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['prepseccion/index']) ?>" style="margin-left: 10px;"><i class="fa fa-fax"></i> Secciones</a>
            </li>
            <?PHP } ?>
            <?PHP if (PerfilUsuario::hasPermiso('1fdee8d8-ef29-4966-badf-3a796b0e1570', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'prepcasillaseccion/') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['prepcasillaseccion/index']) ?>" style="margin-left: 10px;"><i class="fa fa-qrcode"></i> Casillas por Sección</a>
            </li>
            <?PHP } ?>
            <?PHP if (PerfilUsuario::hasPermiso('1fdee8d8-ef29-4966-badf-3a796b0e1570', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'preppartido/') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['preppartido/index']) ?>" style="margin-left: 10px;"><i class="fa fa-users"></i> Partidos Politicos</a>
            </li>
            <?PHP } ?>
            <?PHP if (PerfilUsuario::hasPermiso('1fdee8d8-ef29-4966-badf-3a796b0e1570', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'prepcandidato/') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['prepcandidato/index']) ?>" style="margin-left: 10px;"><i class="fa fa-user"></i> Candidatos</a>
            </li>
            <?PHP } ?>
            <?PHP if (PerfilUsuario::hasPermiso('7b8b4d91-2b1d-4a65-9f29-a4c2f22c9b8f', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'prepvoto/') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['prepvoto/index']) ?>" style="margin-left: 10px;"><i class="fa fa-archive"></i> Votos</a>
            </li>
            <?PHP } ?>
            <?PHP if (PerfilUsuario::hasPermiso('19cff826-d301-48bd-a824-960c67b7d6f6', 'R')) { ?>
            <li class="<?= stripos(Yii::$app->request->getPathInfo(), 'prepresultado/') !== false ? 'active' : '' ?>">
                <a href="<?= Url::to(['prepresultado/index']) ?>" style="margin-left: 10px;"><i class="fa fa-bar-chart"></i> Resultados</a>
            </li>
            <?PHP } ?>
        </ul>
    </li>
    <?PHP } ?>
</ul>