function buildSelect(id, result) {
    filtro = '<div class="form-group filtroEstructura">'+
            '<label for="'+id+'">'+result[0].DescripcionPuesto+'</label>'+
            '<select id="'+id+'" class="form-control" name="IdPuestoDepende[]" data-nivel='+result[0].Nivel+'>'+
            '<option value="0">Todos</option>';

    for (var i=0; i<result.length; i++) {
        filtro += '<option value="'+result[i].IdNodoEstructuraMov+'" data-nivel="'+
            result[i].Nivel+'">'+result[i].DescripcionEstructura+'</option>';
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
                        agregaPuesto(result, id);
                    }
                },
            "json").done(function(){ $('#loadIndicator').hide(); });
    } else {
        $(this).parent().nextAll().remove();
    }
}

$(document).ready(function(){
    $form = $('<form action="" method="post" target="_blank">'+
            '<input type="text" name="title" id="title">'+
            '<textarea name="content" id="content"></textarea>'+
            '<input type="hidden" name="_csrf" value="'+$('[name=_csrf]').val()+'"></form>');

    $('.btnExportPdf, .btnExportExcel').click(function(event){
        content = $('#reporteContainer').html();

        if ($(this).attr('id') == 'btnExportExcel') {
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

        var idMuni = $(this).val();

        if (idMuni != '') {
            $.getJSON(urlPuestos+'?_csrf='+$('[name=_csrf]').val()+'&idMuni='+idMuni, function(result) {
                $("#bodyForm").append('<br><div class="form-group"><label>Seleccione el nivel de estructura: </label></div><br>');
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

    $('#btnGenerarReporte').click(function(event) {
        $('.alert').remove();

        if ($('#municipio').val() == '') {
            $('#bodyForm').append('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar un municipio</div>');
            return false;
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