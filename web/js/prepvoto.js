$(document).ready(function(){
    $('#tipoEleccion').change(function(){
        $('#municipio').closest('div').addClass('hidden');
        $('#distritoLocal').closest('div').addClass('hidden');
        $('#distritoFederal').closest('div').addClass('hidden');
        $('#municipio option:first').prop('selected', true);
        $('#distritoLocal option:first').prop('selected', true);
        $('#distritoFederal option:first').prop('selected', true);
        $('#alertResult').html('');
        
        switch ($(this).val()) {
            case '1': // Presidencia Municipal
                $('#municipio').closest('div').removeClass('hidden');
                break;
            case '2': //
                $('#distritoLocal').closest('div').removeClass('hidden');
                break;
            case '3': //
                $('#distritoFederal').closest('div').removeClass('hidden');
                break;
        }
    });

    $('#btnAceptar').click(function(){
        $('#alertResult').html('');
        
        if ($('#tipoEleccion').val() == '' || typeof($('#tipoEleccion').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Tipo de Elección</div>');
            return false;
        }

        switch ($('#tipoEleccion').val()) {
            case '1': // Presidencia Municipal
                    //eleccionPresidenciaMunicipal();
                    if ($('#municipio').val() == '' || typeof($('#municipio').val()) == 'undefined') {
                        $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Municipio</div>');
                        return false;
                    }
                break;
            case '2': // Diputación Local
                    //eleccionDiputacionLocal();
                    if ($('#distritoLocal').val() == '' || typeof($('#distritoLocal').val()) == 'undefined') {
                        $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Distrito Local</div>');
                        return false;
                    }
                break;
            case '3': // Diputación Federal
                    //eleccionDiputacionFederal();
                    if ($('#distritoFederal').val() == '' || typeof($('#distritoFederal').val()) == 'undefined') {
                        $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Distrito Federal</div>');
                        return false;
                    }
                break;
        }

        $('#formFiltroVotos').trigger('submit');
    });

    $('.inputVoto').numeric({decimal: false, negative: false});

    $('.inputVoto').change(function() {
        id = $(this).attr('id');
        separado = id.split('-');
        self = this;

        $.ajax({
            url: urlVotar,
            type: 'post',
            dataType: 'json',
            data: 'candidato='+separado[0]+'&casilla='+separado[1]+'&votos='+$(self).val()
        }).done(function(respuesta){
            $('.sumaCandidato-'+separado[0]).text(sumVotosCandidato(separado[0]));
            $('.sumaCasilla-'+separado[1]).text(sumVotosCasilla(separado[1]));
            sumTotalVotos();
            actualizaPorcentajes();
        });
    });

    $('.inputObservacion').change(function() {
        id = $(this).attr('id');
        separado = id.split('-');
        self = this;

        $.ajax({
            url: urlObservacion,
            type: 'post',
            dataType: 'json',
            data: 'casilla='+separado[1]+'&obser='+$(self).val()
        }).done(function(respuesta){
        });
    });
});

function sumVotosCandidato(candidato)
{
    sumaCandidato = 0;

    // selecciona todos los elementos que disponen de ese atributo y cuyo
    // valor comienza exactamente por la cadena de texto indicada
    $('.inputVoto[id^='+candidato+'-]').each(function(){
        sumaCandidato += parseInt($(this).val()) || 0;
    });

    return sumaCandidato;
}

function sumVotosCasilla(casilla)
{
    sumaCasilla = 0;

    // selecciona todos los elementos que disponen de ese atributo y cuyo
    // valor termina exactamente por la cadena de texto indicada.
    $('.inputVoto[id$=-'+casilla+']').each(function(){
        sumaCasilla += parseInt($(this).val()) || 0;
    });

    return sumaCasilla;
}

function sumTotalVotos(casilla)
{
    sumaTotalVotos = 0;

    $('th[class^=sumaCandidato-]').each(function(){
        sumaTotalVotos += parseInt($(this).text()) || 0;
    });

    $('.totalVotos').text(sumaTotalVotos);
}

function actualizaPorcentajes()
{
    sumaTotalVotos = parseInt($('.totalVotos').text()) || 0;

    $('th[class^=porcentaje-]').each(function(){
        separado = $(this).attr('class').split('-');
        porcentaje = sumaTotalVotos!=0 ? Math.round($('th[class=sumaCandidato-'+separado[1]+']').text()/sumaTotalVotos*100) : 0;
        $(this).text(porcentaje + ' %');
    });
}