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
            case '2': // Diputación Local
                $('#distritoLocal').closest('div').removeClass('hidden');
                break;
            case '3': // Diputación Federal
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

        /*if ($('#candidato').val() == '' || typeof($('#candidato').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar al Candidato</div>');
            return false;
        }*/

        if ($('#iniSeccion').val() > $('#finSeccion').val()) {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el rango correcto de las Secciones</div>');
            return false;
        }

        getResultados();
    });

    $('#municipio, #distritoLocal, #distritoFederal').change(function(){ 
        //loadCandidatos.apply(this);
        loadZonas.apply(this);
    });

    $('#zona').change(function(){
        loadSecciones.apply(this);
    });

});

function loadCandidatos()
{
    $('#loadIndicator').show();

    $.ajax({
        url: urlGetCandidatos,
        method: 'POST',
        dataType: 'json',
        data: $('#formFiltroVotos').serialize()
    }).done(function(response) {
        $('#loadIndicator').hide();
        $('#div_candidato').removeClass('hidden');

        $('#candidato option:not(:first)').remove();

        if (response.length) {
            for (candidato in response) {
                $('#candidato').append('<option value="'+response[candidato].id_candidato+'">'+response[candidato].nombre+'</option>');
            }
        }
    });
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

function getResultados()
{
    $('#loadIndicator').show();

    $.ajax({
        url: urlGetResultados,
        method: 'POST',
        dataType: 'json',
        data: $('#formFiltroVotos').serialize()
    }).done(function(response) {
        $('#loadIndicator').hide();
        $('#tabs_resultado').removeClass('hidden');

        fecha = new Date();

        tabla = '<table border="1" cellpadding="1" cellspacing="1" id="" class="table table-condensed table-striped table-bordered table-hover">';
        thead = '<thead><tr><th></th><th>Meta Estimada</th>';
        tbody = '<tbody>';

        listSumVotos = [];
        listCandidatos = [];
        arrVotosCandidatos = [];
        sumaTotal = 0;

        for (candidato in response.candidatos) {
            thead += '<th>'+response.candidatos[candidato].nombre+'</th>';
            listCandidatos.push(response.candidatos[candidato].nombre);
            listSumVotos[response.candidatos[candidato].id_candidato] = 0;
        }

        thead += '<th>Total</th>';

        for (fila in response.resultado) {
            tbody += '<tr><td>Sección '+response.resultado[fila].seccion+'</td>\n\
                    <td>'+response.resultado[fila].meta+'</td>';
            sumaSeccion = 0;

            for (candidato in response.candidatos) {
                if (typeof(response.resultado[fila].votos[response.candidatos[candidato].id_candidato]) == 'undefined') {
                    tbody += '<td>0</td>';
                    listSumVotos[response.candidatos[candidato].id_candidato] += 0;
                } else {
                    tbody += '<td>'+response.resultado[fila].votos[response.candidatos[candidato].id_candidato]+'</td>';
                    listSumVotos[response.candidatos[candidato].id_candidato] += parseInt(response.resultado[fila].votos[response.candidatos[candidato].id_candidato]);
                    sumaSeccion += parseInt(response.resultado[fila].votos[response.candidatos[candidato].id_candidato]);
                }
            }
            sumaTotal += sumaSeccion;
            tbody += '<td>'+sumaSeccion+'</td>';
            tbody += '</tr>';
        }

        for (candidato in response.candidatos) {
            arrVotosCandidatos.push(listSumVotos[response.candidatos[candidato].id_candidato]);
        }

        thead += '<tr><th>Votos</th><th></th>';
        for (candidato in response.candidatos) {
            thead += '<th>'+listSumVotos[response.candidatos[candidato].id_candidato]+'</th>';
        }
        thead += '<th>'+sumaTotal+'</th><tr>';

        thead += '<tr><th>Porcentajes</th><th></th>';
        for (candidato in response.candidatos) {
            thead += '<th>'+(sumaTotal!=0 ? Math.round(listSumVotos[response.candidatos[candidato].id_candidato]/sumaTotal*100) : 0)+' %</th>';
        }
        thead += '<th>100%</th><tr>';
        thead += '</thead>';
        tbody += '</tbody>';
        tabla += thead+tbody+'</table>';

        $('#tabla_resultado').html(tabla);

        var datos_grafica =
        {
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
            "graphset":[
            {
                "type":"bar",
                /*"type":"bar3d",
                "3d-aspect":{
                    "true3d":false,
                    "y-angle":10,
                    "depth":25
                },*/
                "title":{
                    "text":"Resultados Electorales Preliminares",
                    "background-color":"none",
                    "font-color":"black",
                    "border-width":1,
                    "border-color":"#CCCCCC",
                    "bold":true,
                    "border-bottom":"none"
                },
                /*"subtitle":{
                    "text": $('#candidato option:selected').text(),
                    "background-color":"none",
                    "font-color":"black",
                    "border-width":1,
                    "border-color":"#CCCCCC",
                    "bold":true,
                    "border-top":"none"
                },*/
                "source":{
                    "text":"Fecha de Corte: "+String("00000" + fecha.getDate()).slice(-2) + "-" + String("00000" + (fecha.getMonth()+1)).slice(-2) + "-" + fecha.getFullYear()+' '+fecha.getHours()+':'+fecha.getMinutes()
                },
                "border-width":1,
                "border-color":"#CCCCCC",
                "background-color":"#fff #eee",
                //"legend":{},
                "plot":{
                    "valueBox":{
                        "type":"all",
                        "placement":"top",
                        "font-color":"#FFFFFF",
                        "background-color":"#000000",
                        "border-radius":5,
                        "bold":true
                    }
                },
                "tooltip":{
                    "background-color":"#000000",
                    "border-radius":5,
                    "font-color":"#FFFFFF",
                    "bold":true,
                    "padding":5,
                    "text":"%k - %v"
                },
                "scaleX":{
                    "label":{
                        "text":"Candidatos"
                    },
                    "values":listCandidatos,
                    "item":{
                        "font-size":"9px"
                    }
                },
                "scaleY":{
                    "label":{
                        "text":"Votos"
                    },
                },
                "series":[
                    /*{
                        "values":metas,
                        "text":"Meta",
                        "animate":true,
                        "effect":2,
                    },*/
                    {
                        "values":arrVotosCandidatos,
                        "text":"Votos",
                        "animate":true,
                        "effect":2,
                    }
                ]
            }
        ]
        };

        zingchart.render({
            id: "grafica_resultado",
            data: datos_grafica,
            height: 400,
            width: "80%"
        });
        
    });
}