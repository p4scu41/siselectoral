<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="<?= Yii::$app->params['chiapas_unidos'] ? Url::to("@web/img/chiapas_unidos_favicon.png") : Url::to("@web/img/icon.ico") ?>">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>

        <?php $this->head() ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue">
        <?php if (stripos(Yii::$app->request->getPathInfo(), 'prepresultado') === false) { ?>
            <div id="div_loading"> &nbsp; </div>
        <?php } ?>

        <?php $this->beginBody() ?>

        <?php $this->beginContent('@app/views/layouts/header.php'); ?>
        <?php $this->endContent(); ?>

        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?=
                                Yii::$app->params['chiapas_unidos'] == true ? Url::to("@web/img/chiapas_unidos_logo.png") : (Yii::$app->user->identity->persona ? Yii::$app->user->identity->persona->getFoto() : '')
                                ?>" class="img-rounded" />
                        </div>
                        <div class="pull-left info">
                            <p><?= Yii::$app->user->identity->login ?></p>

                            <a href="#"><i class="fa fa-circle text-success"></i> En linea</a>
                        </div>
                    </div>
                    <?php $this->beginContent('@app/views/layouts/menu.php'); ?>
                    <?php $this->endContent(); ?>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1><?= Html::encode($this->title) ?></h1>

                    <?=
                    Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ])
                    ?>
                </section>

                <!-- Main content -->
                <section class="content <?= Yii::$app->params['chiapas_unidos'] ? 'bg_chiapas_solidario' : '' ?>">

                    <?= $content ?>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>
