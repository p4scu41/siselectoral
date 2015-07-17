function buildSelect(id, result) {
    filtro = '<div class="form-group filtroEstructura">'+
            '<label for="'+id+'">'+result[0].DescripcionPuesto+'</label>'+
            '<select id="'+id+'" class="form-control" name="IdPuestoDepende[]" data-nivel='+result[0].Nivel+'>'+
            '<option value="0">Todos</option>';

    for (var i=0; i<result.length; i++) {
        text = result[i].DescripcionEstructura;

        // Para el caso de promotores, agregar el nombre
        if (result[i].Nivel == 7 || result[i].Nivel == 6) {
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

        $(this).parent().nextAll().not('.no-delete').remove();

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
    self = this;

    if( seleccionado!='' && seleccionado!=0) {
        $('#loadIndicator').show();

        $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$('#municipio').val()+
                '&Nivel='+$(this).find('option:selected').data('nivel')+'&IdPuestoDepende='+$(this).val(),
                function(result) {
                    if (result == null) {
                        result = {};
                    }

                    if (result.length>0) {
                        id = doId(result[0].DescripcionPuesto);
                        agregaPuesto.call(self, result, id);
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
            '<input type="text" name="encabezado" id="encabezado">'+
            '<textarea name="content" id="content"></textarea>'+
            '<input type="hidden" name="_csrf" value="'+$('[name=_csrf]').val()+'"></form>');

    $('.btnExportPdf, .btnExportExcel').click(function(event){
        content = $('#reporteContainer').html();
        thead = '<thead>'+$('#tabla_reporte thead').html()+'</thead><tbody>';
        tabla = '<table class="table table-condensed table-bordered table-hover" border="1" cellpadding="1" cellspacing="1">';
        content = content.replace(new RegExp('<tr><td> &nbsp; </td><td class="text-center"> &nbsp; </td><td class="text-center"> &nbsp; </td><td> &nbsp; </td><td class="text-center"> &nbsp; </td><td class="text-center"> &nbsp; </td></tr>', 'g'), '</tbody></table><pagebreak />'+tabla+thead);
        content = content.replace(new RegExp('<tr><td> &nbsp; </td><td class="text-center"> &nbsp; </td><td class="text-center"> &nbsp; </td><td> &nbsp; </td><td class="text-center"> &nbsp; </td><td class="text-center"> &nbsp; </td><td class="text-center"> &nbsp; </td></tr>', 'g'), '</tbody></table><pagebreak />'+tabla+thead);

        if ($(this).hasClass('btnExportExcel')) {
            content = $('#reporteContainer table').table2CSV({delivery: 'value'});
        }

        $form.find('#content').text( content );
        $form.find('#title').val( $('#titulo').html() );
        $form.find('#encabezado').val( 'Mi encabezado perzonalizado' );
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

        var idMuni = $(this).val();

        $("#bodyForm .nivelEstructura, #bodyForm .filtroEstructura").remove();

        if (idMuni != '') {
            // Construye la lista de secciones
            /*$.ajax({
                url: getSeccionesMuni,
                type: 'POST',
                data: '?_csrf='+$('[name=_csrf]').val()+'&municipio=' + $('#municipio').val(),
                dataType: 'json',
            }).done(function(response){
                var secciones = '<div class="form-group filtroEstructura">'+
                    '<label for="jefe-de-secion">Secciones: </label>'+
                    '<select id="jefe-de-secion" class="form-control" name="IdPuestoDepende[]" data-nivel="5">'+
                        '<option value="0">Todos</option>';

                for (seccion in response) {
                    secciones += '<option value="' + response[seccion].IdNodoEstructuraMov+ '" data-nivel="5">' + response[seccion].NumSector+' '+response[seccion].NOMBRECOMPLETO+'</option>';
                }

                secciones += '</select></div>';

                $("#bodyForm").append(secciones);
                $("#jefe-de-secion").change(agregaPuestoDepende);
                $('#loadIndicator').hide();
            });*/
            // Inicia la construccion dinamica de los selects de la estructura
            $.getJSON(urlPuestos+'?_csrf='+$('[name=_csrf]').val()+'&idMuni='+idMuni, function(result) {
                if (result.length>0) {
                    $("#bodyForm").append('<div class="form-group nivelEstructura"><label>Seleccione el nivel de estructura: </label><br></div>');

                    id = doId(result[0].Descripcion);
                    $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+idMuni,
                        function(result){ agregaPuesto.call(self, result, id); }, "json")
                        .done(function(){ $('#loadIndicator').hide(); });
                } else {
                    $('#loadIndicator').hide();
                }
            });
        } else {
            $('#loadIndicator').hide();
        }
    });

    $('#btnGenerarReporte').click(function(event) {
        $('.alert').remove();

        if ($('#municipio').val() == '') {
            $('#bodyForm').append('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar un municipio</div>');
            return false;
        }

        if ($('[name="tipo_promovido"][type="radio"]').length) {
            if ($('[name=tipo_promovido]:checked').val() == undefined) {
                $('#bodyForm').append('<div class="alert alert-danger" role="alert">'+
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                    '<span aria-hidden="true">&times;</span></button>Debe seleccionar el tipo de reporte: '+
                    'Promovidos efectivos o Listado de Promoción</div>');
                return false;
            }
        }

        $('#loadIndicator').show();
        $('#div_loading').show();

        $.ajax({
            type: 'POST',
            url: urlReporte,
            data: $('#formBuscar').serialize()+'&tipoReporte=3',
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

});