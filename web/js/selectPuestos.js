function buildSelect(id, result) {
    filtro = '<div class="form-group filtroEstructura">'+
            '<label for="'+id+'">'+result[0].DescripcionPuesto+'</label>'+
            '<select id="'+id+'" class="form-control" name="IdPuestoDepende[]" data-nivel='+result[0].Nivel+'>'+
            '<option value="0">Todos</option>';

        for (var i=0; i<result.length; i++) {
            text = result[i].DescripcionEstructura;

            // Para el caso de promotores y coordinador de promotores, agregar el nombre
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
    detalleReporte = $('input:checked:last').val();
    self = this;

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
                        agregaPuesto.call(self, result, id);
                    }
                },
            "json").done(function(){ $('#loadIndicator').hide(); });
    } else {
        $(this).parent().nextAll().remove();
    }
}