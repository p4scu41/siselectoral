$(document).ready(function(){

    $('#tipoEleccion').change(function(){
        $('#municipio').closest('div').addClass('hidden');
        $('#distritoLocal').closest('div').addClass('hidden');
        $('#distritoFederal').closest('div').addClass('hidden');
        $('#div_zonas').closest('div').addClass('hidden');

        $('#municipio option:first').prop('selected', true);
        $('#distritoLocal option:first').prop('selected', true);
        $('#distritoFederal option:first').prop('selected', true);
        $('#zona option:not(:first)').remove();
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
                    if ($('#municipio').val() == '' || typeof($('#municipio').val()) == 'undefined') {
                        $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Municipio</div>');
                        return false;
                    }
                break;
            case '2': // Diputación Local
                    if ($('#distritoLocal').val() == '' || typeof($('#distritoLocal').val()) == 'undefined') {
                        $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Distrito Local</div>');
                        return false;
                    }
                break;
            case '3': // Diputación Federal
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

    $('#fechaCorte').datetimepicker({
		timeOnlyTitle: 'Elegir una hora',
		timeText: 'Hora',
		hourText: 'Horas',
		minuteText: 'Minutos',
		secondText: 'Segundos',
		millisecText: 'Milisegundos',
		microsecText: 'Microsegundos',
		timezoneText: 'Uso horario',
		currentText: 'Hoy',
		closeText: 'Aceptar',
		timeFormat: 'HH:mm',
		timeSuffix: '',
		amNames: ['a.m.', 'AM', 'A'],
		pmNames: ['p.m.', 'PM', 'P'],
		isRTL: false,
        dateFormat: 'dd-mm-yy',
	});

    setInterval(function(){
        $('#btnAceptar').trigger('click');
    }, 60000);

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

        if ($('#fechaCorte').val()) {
            inputFecha = $('#fechaCorte').val();
            arrayFecha = inputFecha.split(' ');
            fechaFull = arrayFecha[0].split('-');
            fecha = new Date(fechaFull[2]+'-'+fechaFull[1]+'-'+fechaFull[0]+' '+arrayFecha[1]);
        } else {
            fecha = new Date();
        }

        tabla = '<table border="1" cellpadding="1" cellspacing="1" id="" class="table table-condensed table-striped table-bordered table-hover">';
        thead = '<thead><tr><th></th><th>Meta Estimada</th>';
        tbody = '<tbody>';

        listSumVotos = [];
        listCandidatos = [];
        arrVotosCandidatos = [];
        colores = [];
                        /*{
                            "rule":"%i == 0",
                            "background-color":"black",
                            "line-color":"black"
                        },
                        {
                            "rule":"%i == 1",
                            "background-color":"red",
                            "line-color":"red"
                        },
                        {
                            "rule":"%i == 2",
                            "background-color":"yellow",
                            "line-color":"yellow"
                        },
                    ]*/
        sumaTotal = 0;

        for (candidato in response.candidatos) {
            thead += '<th>'+response.candidatos[candidato].nombre+'</th>';
            listCandidatos.push(response.candidatos[candidato].nombre);
            listSumVotos[response.candidatos[candidato].id_candidato] = 0;
            colores.push({
                "rule":"%i == "+candidato,
                "background-color":response.candidatos[candidato].color,
                "line-color":response.candidatos[candidato].color
            });
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

        totales_casillas = '<h4 class="text-center">Total de Casillas: '+response.totalCasillas+
            ', Casillas Contabilizadas: '+response.casillasConsideradas+
            ', Porcentaje Contabilizadas: '+(response.totalCasillas!=0 ? Math.round(response.casillasConsideradas/response.totalCasillas*100) : 0)+'%</h4>';

        $('#tabla_resultado').html(tabla);
        $('#totales_casillas').html(totales_casillas);

        for (index in listCandidatos) {
            listCandidatos[index] = listCandidatos[index].replace(/\s+/g, '\n');
        }

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
                    "text":"Fecha de Corte: "+String("00000" + fecha.getDate()).slice(-2) + "-" + String("00000" + (fecha.getMonth()+1)).slice(-2) + "-" + fecha.getFullYear()+' '+String("00000" + fecha.getHours()).slice(-2)+':'+String("00000" + fecha.getMinutes()).slice(-2)
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
                    },
                    "rules": colores
                },
                "plotarea": {
                    "margin": "65px 65px 120px 80px"
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
                        "text":"Candidatos",
                        "padding-top": "30px",
                        //"padding-bottom": "0px"
                    },
                    "values":listCandidatos,
                    "item":{
                        "font-size":"9px",
                        "auto-align":true,
                        //"font-angle": -48
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
            width: "100%"
        });
        
    });
}
