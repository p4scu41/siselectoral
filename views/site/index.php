<?php
/* @var $this yii\web\View */
$this->title = 'Panel de Control';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(\yii\helpers\Url::to('@web//js/AdminLTE/dashboard.js'), ['position' => \yii\web\View::POS_END]);
?>
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>150</h3>
                <p>Coord. Municipales</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-stalker"></i>
            </div>
            <a href="#" class="small-box-footer">
                Detalles <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3>53%</h3>
                <p>Promovidos</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">
                Detalles <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>44</h3>
                <p>Nuevos Promotores</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">
                Detalles <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3>65</h3>
                <p>Meta alcanzada</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">
                Detalles <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div><!-- ./col -->
</div><!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7">

        <!-- Custom tabs (Charts with tabs)-->
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a href="#revenue-chart" data-toggle="tab">Gr&aacute;fica</a></li>
                <li class="pull-left header"><i class="fa fa-inbox"></i> Comportamiento de los Votos</li>
            </ul>
            <div class="tab-content no-padding">
                <!-- Morris chart - Sales -->
                <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
            </div>
        </div><!-- /.nav-tabs-custom -->

    </section><!-- /.Left col -->
    <!-- right col (We are only adding the ID to make the widgets sortable)-->
    <section class="col-lg-5">

        <!-- solid sales graph -->
        <div class="box box-solid bg-teal-gradient">
            <div class="box-header">
                <i class="fa fa-th"></i>
                <h3 class="box-title">Tendencia de promoción</h3>
            </div>
            <div class="box-body border-radius-none">
                <div class="chart" id="line-chart" style="height: 250px;"></div>
            </div><!-- /.box-body -->
            <div class="box-footer no-border">
                <div class="row">
                    <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                        <input type="text" class="knob" data-readonly="true" value="20" data-width="60" data-height="60" data-fgColor="#39CCCC"/>
                        <div class="knob-label">Nuevos</div>
                    </div><!-- ./col -->
                    <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                        <input type="text" class="knob" data-readonly="true" value="50" data-width="60" data-height="60" data-fgColor="#39CCCC"/>
                        <div class="knob-label">Meta</div>
                    </div><!-- ./col -->
                    <div class="col-xs-4 text-center">
                        <input type="text" class="knob" data-readonly="true" value="30" data-width="60" data-height="60" data-fgColor="#39CCCC"/>
                        <div class="knob-label">Promedio</div>
                    </div><!-- ./col -->
                </div><!-- /.row -->
            </div><!-- /.box-footer -->
        </div><!-- /.box -->

    </section><!-- right col -->
</div><!-- /.row (main row) -->

<div class="row">
    <div class="col-md-6">
        <!-- AREA CHART -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Area Chart</h3>
            </div>
            <div class="box-body chart-responsive">
                <div class="chart" id="revenue-chart2" style="height: 300px;"></div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->

        <!-- DONUT CHART -->
        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Donut Chart</h3>
            </div>
            <div class="box-body chart-responsive">
                <div class="chart" id="sales-chart" style="height: 300px; position: relative;"></div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->

    </div><!-- /.col (LEFT) -->
    <div class="col-md-6">
        <!-- LINE CHART -->
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">Line Chart</h3>
            </div>
            <div class="box-body chart-responsive">
                <div class="chart" id="line-chart2" style="height: 300px;"></div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->

        <!-- BAR CHART -->
        <div class="box box-success">
            <div class="box-header">
                <h3 class="box-title">Bar Chart</h3>
            </div>
            <div class="box-body chart-responsive">
                <div class="chart" id="bar-chart" style="height: 300px;"></div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->

    </div><!-- /.col (RIGHT) -->
</div><!-- /.row -->