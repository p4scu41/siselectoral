$(document).ready(function(){
    $('#municipio').change(function(){
        $('#loadIndicator').show();
        $.ajax({
            url: getSeccionesMuni,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&municipio=' + $('#municipio').val(),
            dataType: 'json',
        }).done(function(response){
            var secciones = ''+
                '<label for="list-jefe-de-seccion">Secciones: </label>'+
                '<select id="list-jefe-de-seccion" class="form-control" name="seccion">'+
                    '<option value="0">Jefes de Sección</option>';

            for (seccion in response) {
                secciones += '<option value="' + response[seccion].IdNodoEstructuraMov+ '" data-nivel="5">'+response[seccion].NumSector+' '+response[seccion].NOMBRECOMPLETO+'</option>';
            }

            secciones += '</select>';

            $('#listJefeSeccion').html(secciones);
            $('#loadIndicator').hide();
        });
    });

    $('#btnBuscar').click(function(){
        if ($('#municipio').val() == 0 || typeof($('#municipio').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Municipio</div>');
            return false;
        }

        if ($('#list-jefe-de-seccion').val() == 0 || typeof($('#list-jefe-de-seccion').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Jefe de Sección</div>');
            return false;
        }

        $('#alertResult').html('');
        $('#bingoListPromovidos').html('');
        $('#loadIndicator').show();

        $.ajax({
            url: getPromotoresBySeccion,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&nodo=' + $('#list-jefe-de-seccion').val(),
            dataType: 'json'
        }).done(function(response){
            if (response.length == 0) {
                $('#alertResult').html('<div class="alert alert-danger">No se encontraron Promotores con faltantes</div>');
                $('#bingoListPromotores').html('');
            } else {
                listPromotores = '<ul class="list-group">';

                for (promotor in response) {
                    listPromotores += '<li class="list-group-item" data-id="'+response[promotor].idNodo+'"><span class="badge">'+
                        response[promotor].faltantes+'</span>'+response[promotor].NOMBRECOMPLETO+'</li>';
                }

                listPromotores += '</ul>';
                $('#bingoListPromotores').html(listPromotores);
            }

            $('#loadIndicator').hide();
        });
    });

    $('#bingoListPromotores').delegate('.list-group-item', 'click', function(){
        $('.list-group-item').removeClass('active');
        $(this).addClass('active');
        $('#loadIndicator').show();
        self = this;

        $.ajax({
            url: getPromovidosBySeccion,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&promotor=' + $(self).data('id'),
            dataType: 'json'
        }).done(function(response){
            if (response.length == 0) {
                $('#alertResult').html('<div class="alert alert-danger">No se encontraron Promovidos para el Promotor seleccionado</div>');
                $('#bingoListPromovidos').html('');
            } else {
                listPromovidos = '';

                for (promovido in response) {
                    listPromovidos += '<div class="media">'+
                        '<div class="media-left">'+
                            '<img class="media-object" src="'+response[promovido].foto+'" style="width: 64px; height: 64px;">'+
                        '</div>'+
                        '<div class="media-body">'+
                            '<h5 class="media-heading"><label class=""><input type="checkbox" value="'+response[promovido].CLAVEUNICA+'"> '+
                            response[promovido].NOMBRECOMPLETO+'</label></h5>'+response[promovido].COLONIA+
                        '</div>'+
                    '</div>';
                }

                $('#bingoListPromovidos').html(listPromovidos);
            }

            $('#loadIndicator').hide();
        });
    });

    /*$('#bingoListPromovidos').delegate('input[type="checkbox"]', 'click', function(){
        if ($(this).prop('checked')) {
            console.log($(this).data('id'));
            $(this).closest('.media').fadeOut('slow', function(){ $(this).remove(); });
        }
    });*/

    $('#btnSeleccionarTodos').click(function(){
        $('#bingoListPromovidos input[type="checkbox"]').prop('checked', true);
    });

    $('#btnParticipar').click(function(){
        listPromovidos = $('#bingoListPromovidos input[type="checkbox"]:checked');
        promovidos = '';

        listPromovidos.each(function(){
            promovidos += $(this).val()+'|';
        });

        $('#loadIndicator').show();
        $.ajax({
            url: setParticipacion,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&promovidos=' + promovidos,
            dataType: 'json',
        }).done(function(response){
            $('#btnBuscar').trigger('click');
            $('#bingoListPromovidos').html('');
        });
    });

});