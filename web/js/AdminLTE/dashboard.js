/*
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This is a demo file used only for the main dashboard (index.html)
 **/

$(function() {
    "use strict";

    /* jQueryKnob */
    $(".knob").knob();

    //Sparkline charts
    var myvalues = [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021];
    $('#sparkline-1').sparkline(myvalues, {
        type: 'line',
        lineColor: '#92c1dc',
        fillColor: "#ebf4f9",
        height: '50',
        width: '80'
    });
    myvalues = [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921];
    $('#sparkline-2').sparkline(myvalues, {
        type: 'line',
        lineColor: '#92c1dc',
        fillColor: "#ebf4f9",
        height: '50',
        width: '80'
    });
    myvalues = [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21];
    $('#sparkline-3').sparkline(myvalues, {
        type: 'line',
        lineColor: '#92c1dc',
        fillColor: "#ebf4f9",
        height: '50',
        width: '80'
    });

    /* Morris.js Charts */
    // Sales chart
    if( location.href.indexOf('login') == -1 ) {
        var area = new Morris.Area({
            element: 'revenue-chart',
            resize: true,
            data: [
                {y: '2011 Q1', item1: 2666, item2: 2666},
                {y: '2011 Q2', item1: 2778, item2: 2294},
                {y: '2011 Q3', item1: 4912, item2: 1969},
                {y: '2011 Q4', item1: 3767, item2: 3597},
                {y: '2012 Q1', item1: 6810, item2: 1914},
                {y: '2012 Q2', item1: 5670, item2: 4293},
                {y: '2012 Q3', item1: 4820, item2: 3795},
                {y: '2012 Q4', item1: 15073, item2: 5967},
                {y: '2013 Q1', item1: 10687, item2: 4460},
                {y: '2013 Q2', item1: 8432, item2: 5713}
            ],
            xkey: 'y',
            ykeys: ['item1', 'item2'],
            labels: ['Item 1', 'Item 2'],
            lineColors: ['#a0d0e0', '#3c8dbc'],
            hideHover: 'auto'
        });
        var line = new Morris.Line({
            element: 'line-chart',
            resize: true,
            data: [
                {y: '2011 Q1', item1: 2666},
                {y: '2011 Q2', item1: 2778},
                {y: '2011 Q3', item1: 4912},
                {y: '2011 Q4', item1: 3767},
                {y: '2012 Q1', item1: 6810},
                {y: '2012 Q2', item1: 5670},
                {y: '2012 Q3', item1: 4820},
                {y: '2012 Q4', item1: 15073},
                {y: '2013 Q1', item1: 10687},
                {y: '2013 Q2', item1: 8432}
            ],
            xkey: 'y',
            ykeys: ['item1'],
            labels: ['Item 1'],
            lineColors: ['#efefef'],
            lineWidth: 2,
            hideHover: 'auto',
            gridTextColor: "#fff",
            gridStrokeWidth: 0.4,
            pointSize: 4,
            pointStrokeColors: ["#efefef"],
            gridLineColor: "#efefef",
            gridTextFamily: "Open Sans",
            gridTextSize: 10
        });

        //Fix for charts under tabs
        $('.box ul.nav a').on('shown.bs.tab', function(e) {
            area.redraw();
        });

        // AREA CHART
        var area = new Morris.Area({
            element: 'revenue-chart2',
            resize: true,
            data: [
                {y: '2011 Q1', item1: 2666, item2: 2666},
                {y: '2011 Q2', item1: 2778, item2: 2294},
                {y: '2011 Q3', item1: 4912, item2: 1969},
                {y: '2011 Q4', item1: 3767, item2: 3597},
                {y: '2012 Q1', item1: 6810, item2: 1914},
                {y: '2012 Q2', item1: 5670, item2: 4293},
                {y: '2012 Q3', item1: 4820, item2: 3795},
                {y: '2012 Q4', item1: 15073, item2: 5967},
                {y: '2013 Q1', item1: 10687, item2: 4460},
                {y: '2013 Q2', item1: 8432, item2: 5713}
            ],
            xkey: 'y',
            ykeys: ['item1', 'item2'],
            labels: ['Item 1', 'Item 2'],
            lineColors: ['#a0d0e0', '#3c8dbc'],
            hideHover: 'auto'
        });

        // LINE CHART
        var line = new Morris.Line({
            element: 'line-chart2',
            resize: true,
            data: [
                {y: '2011 Q1', item1: 2666},
                {y: '2011 Q2', item1: 2778},
                {y: '2011 Q3', item1: 4912},
                {y: '2011 Q4', item1: 3767},
                {y: '2012 Q1', item1: 6810},
                {y: '2012 Q2', item1: 5670},
                {y: '2012 Q3', item1: 4820},
                {y: '2012 Q4', item1: 15073},
                {y: '2013 Q1', item1: 10687},
                {y: '2013 Q2', item1: 8432}
            ],
            xkey: 'y',
            ykeys: ['item1'],
            labels: ['Item 1'],
            lineColors: ['#3c8dbc'],
            hideHover: 'auto'
        });

        var colors = new Array('aqua', 'green', 'yellow', 'red', 'blue', 'purple', 'maroon', 'teal');
        var icons = new Array('person-stalker', 'stats-bars', 'person-add', 'pie-graph', 'connection-bars', 'home', 'social-buffer', 'easel');


        $('#btnVerResumen').click(function(event){
            var $municipio = $('#municipio').val();
            $('#formMunicipio').find('.alert').remove();

            if($municipio == '') {
                $('#formMunicipio').append( '<div class="alert alert-success" role="alert">'+
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                    'Debe seleccionar un municipio</div>' );
            } else {
                $.ajax({
                    url: urlResumen,
                    dataType: "json",
                    data: {_csrf: $('[name=_csrf]').val(),idMuni: $municipio},
                    type: "GET",
                }).done(function(response){
                    if (response.length == 0) {
                         $('#formMunicipio').append( '<div class="alert alert-success" role="alert">'+
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                            'No se encontraron datos del municipio</div>' );
                    } else {
                        $('#indicadoresMunicipio').html('');

                        for(var dato in response) {
                            var indicador = '<div class="col-lg-3 col-xs-6">'+
                                '<div class="small-box bg-'+colors[dato]+'">'+
                                    '<div class="inner">'+
                                        '<h3>'+response[dato]['Avances %']+'%</h3>'+
                                        '<p>'+response[dato]['Puesto']+'</p>'+
                                    '</div>'+
                                    '<div class="icon">'+
                                        '<i class="ion ion-'+icons[dato]+'"></i>'+
                                    '</div>'+
                                    '<a href="javascript:irDetalleEstructura()" class="small-box-footer">'+
                                        'Detalles <i class="fa fa-arrow-circle-right"></i>'+
                                    '</a>'+
                                '</div>'+
                            '</div>';

                            $('#indicadoresMunicipio').append(indicador);
                        }
                    }
                });
            }
        });

        $('#btnVerEstructura, .btnDetalleEstructura').click(irDetalleEstructura);
    }
});

function irDetalleEstructura(event) {
    var $municipio = $('#municipio').val();
    $('#formMunicipio').find('.alert').remove();

    if($municipio == '') {
        $('#formMunicipio').append( '<div class="alert alert-success" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            'Debe seleccionar un municipio</div>' );
    } else {
        $.cookie('parametros', '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$municipio);
        location.href = urlPositionTree;
    }
}