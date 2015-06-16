/*
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This is a demo file used only for the main dashboard (index.html)
 **/

$(function() {
    "use strict";

    /* Morris.js Charts */
    // Sales chart
    if( location.href.indexOf('login') == -1 ) {
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

        $('#municipio option:nth-child(2)').prop('selected', true);
        $('#btnVerResumen').trigger('click');

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