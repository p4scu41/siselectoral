function buildSelect(id, result) {
    filtro = '<div class="form-group filtroEstructura">'+
            '<label for="'+id+'">'+result[0].DescripcionPuesto+'</label>'+
            '<select id="'+id+'" class="form-control" name="IdPuestoDepende[]" data-nivel='+result[0].Nivel+'>'+
            '<option value="0">Todos</option>';

        for (var i=0; i<result.length; i++) {
            text = result[i].DescripcionEstructura;

            // Para el caso de promotores, agregar el nombre
            if (result[i].Nivel == 7) {
                text += ' ' + result[i].NOMBRECOMPLETO;
            }

            filtro += '<option value="'+result[i].IdNodoEstructuraMov+'" data-nivel="'+
                result[i].Nivel+'">'+text+'</option>';
        }

        filtro += '</select></div>';

        $objFiltro = $(filtro);

        return $objFiltro.clone(true);
}

function agregaPuesto(result, id) {
    if (result.length>0) {
        $objFiltro = buildSelect(id, result);
        $objFiltro.find('select').change(agregaPuestoDepende);

        if($('#'+id).length) {
            $('#'+id).parent().replaceWith($objFiltro);
            $('#'+id).parent().nextAll().remove();
        } else {
            $('#bodyForm').append($objFiltro);
        }
    }
}

function agregaPuestoDepende() {
    nivel = $(this).data('nivel');
    seleccionado = $(this).val();
    detalleReporte = $('input:checked:last').val();

    if( seleccionado!='' && seleccionado!=0 && nivel<detalleReporte) {
        $('#loadIndicator').show();

        $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$('#municipio').val()+
                '&Nivel='+$(this).find('option:selected').data('nivel')+'&IdPuestoDepende='+$(this).val(),
                function(result) {
                    if (result == null) {
                        result = {};
                    }

                    if (result.length>0) {
                        id = doId(result[0].DescripcionPuesto);
                        agregaPuesto(result, id);
                    }
                },
            "json").done(function(){ $('#loadIndicator').hide(); });
    } else {
        $(this).parent().nextAll().remove();
    }
}

$(document).ready(function(){
    $form = $('<form action="" method="post" target="_blank" style="display:none">'+
            '<input type="text" name="title" id="title">'+
            '<textarea name="content" id="content"></textarea>'+
            '<input type="hidden" name="_csrf" value="'+$('[name=_csrf]').val()+'"></form>');

    $('.btnExportPdf, .btnExportExcel').click(function(event){
        content = $('#reporteContainer').html();

        if ($(this).hasClass('btnExportExcel')) {
            content = $('#reporteContainer table').table2CSV({delivery: 'value'});
        }

        $form.find('#content').text( content );
        $form.find('#title').val( $('#titulo').html() );
        $form.attr('action', $(this).data('url'));
        $('#formExport').html($form);
        $form.submit();

        event.stopPropagation();
        event.preventDefault();
        return false;
    });

    $('#municipio').change(function(){
        $('#loadIndicator').show();
        $('.filtroEstructura').remove();

        var options = '<br class="filtroEstructura"/><div class="form-group filtroEstructura"><label>Seleccione el detalle del reporte: </label> <br class="filtroEstructura"/>&nbsp; ';

        var idMuni = $(this).val();

        if (idMuni != '') {
            $.getJSON(urlPuestos+'?_csrf='+$('[name=_csrf]').val()+'&idMuni='+idMuni, function(result) {
                for (var i=0; i<result.length; i++) {
                    //options += '<option value="'+result[i].IdPuesto+'" data-nivel="'+result[i].Nivel+'">'+result[i].Descripcion+'</option>';
                    options += '<div class="checkbox filtroEstructura"> &nbsp; <label>'+
                            '<input type="checkbox" name="puestos[]" value="'+result[i].IdPuesto+'" '+
                            'data-nivel="'+result[i].Nivel+'" class="chkPuesto" checked> '+
                            result[i].Descripcion+' </label> &nbsp; </div>';
                }
                options += '</div><br class="filtroEstructura"/>';
                $("#bodyForm").append(options);
                $('.chkPuesto').iCheck({
                    checkboxClass: 'icheckbox_minimal-green',
                    radioClass: 'iradio_minimal-green',
                });
                $("#bodyForm").append('<div class="form-group filtroEstructura"><label>Seleccione el nivel de estructura: </label></div><br class="filtroEstructura"/>');
            }).done(function(result) {
                if (result.length>0) {
                    id = doId(result[0].Descripcion);
                    $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+idMuni,
                        function(result){ agregaPuesto(result, id); }, "json")
                        .done(function(){ $('#loadIndicator').hide(); });
                } else {
                    $('#loadIndicator').hide();
                }
            });
        } else {
            $('#loadIndicator').hide();
        }
    });

    $('#btnGenerarReporte, #btnReporteSeccional').click(function(event) {
        $('.alert').remove();

        if ($('#municipio').val() == '') {
            $('#bodyForm').append('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar un municipio</div>');
            return false;
        }

        tipoReporte = '';

        if ($(this).prop('id') == 'btnReporteSeccional') {
            tipoReporte = 1;
        } else if ($(this).prop('id') == 'btnGenerarReporte') {
            tipoReporte = 2;
        }

        $('#loadIndicator').show();
        $('#div_loading').show();

        $.ajax({
            type: 'POST',
            url: urlReporte,
            data: $('#formBuscar').serialize()+'&tipoReporte='+tipoReporte,
            dataType: 'json',
            success: function(result) {
                $('#titulo').html(result.titulo);
                $('#tabla_reporte').html(result.reporteHTML);
                $('#loadIndicator').hide();
                $('.opcionesExportar').show();
                $('#div_loading').fadeOut('slow');
            }
        });
    });

    $('#btnResumen').click(function() {
        $('.alert').remove();

        if ($('#municipio').val() == '') {
            $('#bodyForm').append('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar un municipio</div>');
            return false;
        }

        $('#loadIndicator').show();
        $('#modalResumen .table-responsive').html('<i class="fa fa-refresh fa-spin text-center" style="font-size: x-large; display: inline-block;"></i>');
        $.ajax({
            url: urlResumen,
            dataType: "json",
            data: {_csrf: $('[name=_csrf]').val() ,idMuni: $('#municipio').val()},
            type: "GET",
        }).done(function(response){
            $('#loadIndicator').hide();
            if (response.length == 0) {
                $('#alertResult').html('No se encontraron resultados en la b&uacute;squeda');
                $('#alertResult').show();
            } else {
                var fecha = new Date();
                $('#alertResult').hide();
                tablaResumen = $(ConvertJsonToTable(response, 'tablaResumen', 'table table-condensed table-striped table-bordered table-hover', 'Download'));
                $('#modalResumen .table-responsive').html(tablaResumen);

                $('<tr><td colspan="5">&nbsp;</td></tr>').insertBefore( tablaResumen.find('tr:last'));

                $('#tituloResumen').html(' Municipal de '+$('#municipio option:selected').text());
                $('#fechaResumen').html('Fecha de corte: '+padLeft(fecha.getDate(),2)+'-'+padLeft((fecha.getMonth()+1),2)+'-'+fecha.getFullYear());
                $('#modalResumen').modal('show');
            }
        });
    });

});