var datos_grafica = {
    "gui": {
        "behaviors": [
            {
                "id": "ViewSource",
                "enabled": "none"
            },
            {
                "id": "About",
                "enabled": "none"
            },
            {
                "id": "BuyLicense",
                "enabled": "none"
            },
            {
                "id": "LogScale",
                "enabled": "none"
            },
            {
                "id": "Reload",
                "enabled": "none"
            }
        ]
    },
    "graphset":[{
        "type":"bar",
        "stacked": true,
        "stack-type": "normal",
        "border-width":1,
        "border-color":"#CCCCCC",
        "background-color":"#fff #eee",
        "scaleX":{
            "visible": false,
        },
        "scaleY":{
            "visible": false,
        },
        "tooltip":{
            "visible": false,
        },
        plotarea : {
            width : '100%',
            height : '100%',
            margin : '0 0 0 0'
        },
        "series":[
            {
                "values":[30],
                "text":"Meta",
                "animate":true,
                "effect":2,
                "stack": 1,
                "background-color":"#008d4c",
            },
            {
                "values":[100],
                "text":"Votos",
                "animate":true,
                "effect":2,
                "stack": 1,
                "background-color":"#dff0d8",
            }
        ]
    }]
};

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

        if ($('#iniSeccion').val() != '') {
            if ($('#finSeccion').val() == '' || typeof($('#finSeccion').val()) == 'undefined') {
                $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Fin de las Secciones</div>');
                return false;
            }

            if ($('#iniSeccion').val() > $('#finSeccion').val()) {
                $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el rango correcto de las Secciones</div>');
                return false;
            }
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
            creaGraficas();
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

    $('#municipio, #distritoLocal, #distritoFederal').change(function(){
        loadZonas.apply(this);
    });

    $('#zona').change(function(){
        loadSecciones.apply(this);
    });

    //$('.sparkline').sparkline('html', {type: 'bar'});

    /*$(".sparkline").each(function() {
        var $this = $(this);
        $this.sparkline('html', $this.data());
    });*/

    creaGraficas();

    $('#btnImprimirVotos').click(function(){
        fecha = new Date();
        titulo = '';

        switch ($('#tipoEleccion').val()) {
            case '1': // Presidencia Municipal
                    titulo = 'de '+$('#municipio option:selected').text();
                break;
            case '2': // Diputación Local
                    titulo = 'del Distrito Local '+$('#distritoLocal').val();
                break;
            case '3': // Diputación Federal
                    titulo = 'del Distrito Federal '+$('#distritoFederal').val();
                break;
        }

        if ($('#zona').val()) {
            titulo += ', Zona '+$('#zona').val();
        }

        if ($('#iniSeccion').val()) {
            titulo += ', Secciones '+$('#iniSeccion').val()+' - '+$('#finSeccion').val();
        }

        $imprimible = $('<div class="box box-primary box-success"><div class="panel panel-success" id="containerPerson" style="margin-bottom: 1px !important;">'+
                '<div class="panel-body">'+
                '<h4 class="text-center">Registro de votos '+titulo+'</h4>'+
                ' '+ $('#tablaVotos').html()+
                ' <div class="pull-right">Fecha de corte: '+String("00000" + fecha.getDate()).slice(-2) + "-" + String("00000" + (fecha.getMonth()+1)).slice(-2) + "-" + fecha.getFullYear()+' '+String("00000" + fecha.getHours()).slice(-2)+':'+String("00000" + fecha.getMinutes()).slice(-2)+'</div>'+
                '</div></div></div>');

        $($imprimible).printArea({"mode":"popup","popClose":true});
    });

});

function creaGraficas()
{
    $('.mini_grafica').each(function(){
        datos_grafica.graphset[0].series[0].values[0] = $(this).data('valor');
        datos_grafica.graphset[0].series[1].values[0] = 100 - datos_grafica.graphset[0].series[0].values[0];
        datos_grafica.graphset[0].series[0]['background-color'] = $(this).data('color');

        zingchart.render({
            id: $(this).attr('id'),
            data: datos_grafica,
            height: 120,
            width: 70
        });
    });
}

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
        $('#mini_grafica_'+separado[1]).data('valor', porcentaje);
    });

    creaGraficas();
}

function loadSecciones()
{
    $('#loadIndicator').show();

    $.ajax({
        url: urlGetSecciones,
        method: 'POST',
        dataType: 'json',
        data: $('#formFiltroVotos').serialize()
    }).done(function(response) {
        $('#loadIndicator').hide();
        $('#div_secciones').removeClass('hidden');

        $('#iniSeccion option:not(:first), #finSeccion option:not(:first)').remove();

        if (response.length) {
            for (seccion in response) {
                $('#iniSeccion').append('<option value="'+response[seccion].seccion+'">'+response[seccion].seccion+'</option>');
                $('#finSeccion').append('<option value="'+response[seccion].seccion+'">'+response[seccion].seccion+'</option>');
            }
        }
    });
}

function loadZonas()
{
    $('#loadIndicator').show();

    $.ajax({
        url: urlGetZonas,
        method: 'POST',
        dataType: 'json',
        data: $('#formFiltroVotos').serialize()
    }).done(function(response) {
        $('#loadIndicator').hide();
        $('#div_zonas').removeClass('hidden');

        $('#zona option:not(:first)').remove();

        if (response.length) {
            for (zona in response) {
                $('#zona').append('<option value="'+response[zona].zona+'">'+response[zona].zona+'</option>');
            }
        }
    });
}