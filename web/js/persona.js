/*var mapDomicilio;
var mapCasilla;
myLatlng = new google.maps.LatLng(16.7528099, -93.1154969);

function initialize() {
    var mapOptions = {
        center: myLatlng,
        zoom: 15,
        mapTypeControl: false,
        panControl: false,
        rotateControl: false,
        streetViewControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    mapDomicilio = new google.maps.Map(document.getElementById("mapContainerDomicilio"), mapOptions);
    mapCasilla = new google.maps.Map(document.getElementById("mapContainerCasilla"), mapOptions);

    var contentString = '<div>' +
            '<p><strong>DATOS</strong><br>' +
            'Dirección<br>Referencias<br>' +
            'Responsable<br>' +
            'Otros datos</p>' +
            '</div>';

    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });

    var markerDomicilio = new google.maps.Marker({
        position: myLatlng,
        map: mapDomicilio,
        title: "Domicilio",
        maxWidth: 200,
        maxHeight: 200,
        icon: 'http://www.google.com/intl/en_us/mapfiles/ms/icons/red-dot.png'
    });

    var markerCasilla = new google.maps.Marker({
        position: myLatlng,
        map: mapCasilla,
        title: "Casilla Electoral",
        maxWidth: 200,
        maxHeight: 200,
        icon: 'http://www.google.com/intl/en_us/mapfiles/ms/icons/red-dot.png'
    });

    google.maps.event.addListener(markerDomicilio, 'click', function () {
        infowindow.open(mapDomicilio, markerDomicilio);
    });

    google.maps.event.addListener(markerCasilla, 'click', function () {
        infowindow.open(mapCasilla, markerCasilla);
    });
}

google.maps.event.addDomListener(window, 'load', initialize);

$('#modalMapDomicilio').on('shown.bs.modal', function () {
    google.maps.event.trigger(mapDomicilio, "resize");
    mapDomicilio.setCenter(myLatlng);
});

$('#modalMapCasilla').on('shown.bs.modal', function () {
    google.maps.event.trigger(mapCasilla, "resize");
    mapCasilla.setCenter(myLatlng);
});*/

$(document).ready(function(){
    $.ajax({
        url: urlResumenNodo,
        dataType: "json",
        data: {idNodo: nodo},
        type: "GET",
    }).done(function(response) {
        if(response) {
            meta = response[ response.length-2 ];

            tablaResumenNodo = $(ConvertJsonToTable(response, 'tablaResumen', 'table table-condensed table-striped table-bordered table-hover', 'Download'));

            tablaPromocion = $('<div class="table-responsive"><table class="table table-condensed table-bordered table-hover" id="tablaPromocion">'+
                                '<tr><td colspan="5"><strong>AVANCE DE LA META DE PROMOCIÓN CIUDADA</strong></td></tr>'+
                                '</th><th>Total</th><th>Ocupados</th><th>Vacantes</th><th>Avances %</th></tr></table></div>');
            tablaPromocion.find('#tablaPromocion').append( tablaResumenNodo.find('tr:last') );
            tablaPromocion.find('tr:last td:first').remove();

            tablaResumenNodo.find('tr:last').remove();
            tablaResumenNodo.find('tr:last').remove();

            $('#resumenNodo').html(tablaResumenNodo);
            $('#seccion_promocion').html( tablaPromocion );
        } else {
            $('#no_meta').html('0%');
            $('#resumenNodo').html('');
        }
    });

    // Obtiene los programas
    $.ajax({
        url: urlGetProgramas,
        dataType: "json",
        data: 'idMuni='+municipio,
        type: "GET",
    }).done(function(response){
        count = 0;

        if (response.length) {
            $tabla = '<table border="1" cellpadding="1" cellspacing="1" class="table table-condensed table-striped table-bordered table-hover">'+
                '<thead><tr><th>Nombre</th><th>Integrantes</th><th>Sección</th></tr></thead><tbody>';

            for(fila in response) {
                $tabla += '<tr><td>'+response[fila].Nombre+'</td><td class="text-center">'+response[fila].Integrantes+'</td><td class="text-center">'+
                        '<button class="btn btn-default" type="button" data-idorg="'+response[fila].IdOrganizacion+'">'+
                        '<span class="glyphicon glyphicon glyphicon-th-list" aria-hidden="true"></span></button></td></tr>';
                count++;
            }
            $tabla += '</tbody></table>';


        } else {
            $tabla = 'No hay programas disponibles en este municipio';
            $('#seccion_programas').hide();
        }

        $('#list_programas').html($tabla);
        $('#list_programas button').click(getIntegrantesBySeccion);
        $('#no_programas').html(count);
        $('#list_integrantes').html('');
        $('#list_integrantes').hide();
    });

    function getIntegrantesBySeccion() {
        idorg = $(this).data('idorg');

        $.ajax({
            url: urlGetIntegrantes,
            dataType: "json",
            data: {idOrg:idorg, idMuni:municipio},
            type: "GET",
        }).done(function(response) {
            if ( response.length ) {
                $tabla = 'Distribución de los integrantes por sección<table border="1" cellpadding="1" cellspacing="1" class="table table-condensed table-bordered table-hover">'+
                        '<thead><tr><th class="text-center">Sección</th><th class="text-center">Total</th><th class="text-center">Meta</th><th class="text-center">Integrantes</th></tr></thead><tbody>';

                for(fila in response) {
                    $tabla += '<tr class="text-center"><td>'+parseInt(response[fila].SECCION)+'</td><td>'+response[fila].total+'</td>'+
                            '<td>'+response[fila].MetaAlcanzar+'</td><td><button class="btn btn-default" type="button" data-idorg="'+idorg+'" '+
                            'data-seccion="'+parseInt(response[fila].SECCION)+'"><span class="glyphicon glyphicon glyphicon-th-list" aria-hidden="true"></span></button></tr>';
                }
                $tabla += '</tbody></table>';
            } else {
                $tabla = 'Sin integrantes'
            }
            $('#list_integrantes').html($tabla);
            $('#list_integrantes button').click(listIntegratesFromSeccion);
            $('#list_integrantes').toggle();
        });
    }

    function listIntegratesFromSeccion() {
        $('#modalListIntegrantes .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-pulse fa-lg"></i></div>');

        $.ajax({
            url: urlListInte,
            dataType: "json",
            data: {idOrganizacion:$(this).data('idorg'), idSeccion:$(this).data('seccion')},
            type: "GET",
        }).done(function(response) {
            if ( response.length ) {
                $tabla = '<table border="1" cellpadding="1" cellspacing="1" class="table table-condensed table-bordered table-hover">'+
                        '<thead><tr><th class="text-center">Nombre</th><th class="text-center">Sexo</th><th class="text-center">Fecha Nacimiento</th></tr></thead><tbody>';

                for(fila in response) {
                    $tabla += '<tr><td>'+response[fila].NOMBRE+'</td><td>'+response[fila].SEXO+'</td><td>'+response[fila].FECHANACIMIENTO+'</td></tr>';
                }
                $tabla += '</tbody></table>';
                $('#modalListIntegrantes .modal-body').html($tabla);
            }
        });

        $('#modalListIntegrantes').modal('show');
    }

    // Obtiene la estructura alterna
    $.ajax({
        url: urlTree,//urlTreeAltern
        dataType: "json",
        data: '_csrf='+$('[name=csrf-token]').attr('content')+'&Municipio='+municipio+'&IdPuestoDepende='+puesto+'&alterna=true',
        type: "POST",
    }).done(function(response) {
        if (response.length>0) {
            $('#infoEstrucAlterna span:first').text(response.length);
            treeEstrucAlterna.reload(response);
        } else {
            $('#infoEstrucAlterna span:first').text('0');
            $('#treeEstrucAlterna').hide();
        }
    });

    $("#treeEstrucAlterna").fancytree({
        extensions: ["table"],
        table: {
            indentation: 50
        },
        source: [],
        checkbox: false,
        clickFolderMode: 3,
        lazyLoad: function(event, data) {
            var node = data.node;
            // Issue an ajax request to load child nodes
            data.result = {
                url: urlBranch,
                data: {idNodo: node.key}
            };
        },
        renderColumns: function(event, data) {
            var node = data.node,
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).addClass('text-center');
            if (node.data.persona == '00000000-0000-0000-0000-000000000000') {
                $tdList.eq(1).html('<a href="#" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-user"></span></a>');
            } else {
                $tdList.eq(1).html('<a href="#" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-user"></span></a>');
            }
            $tdList.eq(1).delegate("a", "click", function(e){
                e.stopPropagation();  // prevent fancytree activate for this row
            });
        }
    });

    var treeEstrucAlterna = $("#treeEstrucAlterna").fancytree("getTree");

    $('#meta').click(function(){
        $('#resumenNodo').toggle('slow');
        $('#seccion_promocion').hide();
        $('#seccion_programas').hide();
        $('#treeEstrucAlterna').hide();
    });

    $('#meta_promocion').click(function(){
        $('#seccion_promocion').toggle('slow');
        $('#resumenNodo').hide();
        $('#seccion_programas').hide();
        $('#treeEstrucAlterna').hide();
    });

    $('#btn_programas').click(function(){
        $('#seccion_promocion').hide();
        $('#resumenNodo').hide();
        $('#treeEstrucAlterna').hide();
        $('#seccion_programas').toggle('slow');
    });

    $('#infoEstrucAlterna').click(function(){
        $('#seccion_promocion').hide();
        $('#resumenNodo').hide();
        $('#seccion_programas').hide();
        $('#treeEstrucAlterna').toggle('slow');
    });

});

