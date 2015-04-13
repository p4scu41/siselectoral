$(document).ready(function () {
    'use strict';

    $('#btnBuscarPersona').on('click', function () {

        if ($('#municipio').val() == 0) {
            $('#btnBuscarPersona').after('<div class="alert alert-danger" role="alert" id="alertNoMunicipio">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar un Municipio</div>');
            return false;
        } else {
            $('#alertNoMunicipio').remove();
        }

        $.ajax({
            url: buscarPersona,
            type: 'POST',
            data: $('#frmBuscarPersona').serialize(),
            dataType: 'json',
            beforeSend: function (xhr) {
                $('#tblResultBuscarPersona tbody').html('');
                $('#btnBuscarPersona').after('<i class="fa fa-spinner fa-pulse fa-lg" id="loading"></i>');
            }
        }).done(function (response) {
            var trsPersona = '', persona = 0;

            $('#loading').remove();

            for (persona in response) {
                trsPersona += '<tr>' +
                        '<td><input type="radio" name="persona" value="' + response[persona].CLAVEUNICA+ '"' +
                            'data-nombre="' + response[persona].NOMBRE + ' ' + response[persona].APELLIDO_PATERNO + ' ' + response[persona].APELLIDO_MATERNO + '"' +
                        '></td>' +
                        '<td>' + response[persona].NOMBRE + ' ' + response[persona].APELLIDO_PATERNO + ' ' + response[persona].APELLIDO_MATERNO+ '</td>' +
                        '<td class="seccion_persona">' + parseInt(response[persona].SECCION) + '</td>' +
                        '<td>' + response[persona].CASILLA + '</td>' +
                        '<td>' + response[persona].DOMICILIO + ',' + response[persona].NOM_LOC + '</td>' +
                    '</tr>';
            }

            $('#tblResultBuscarPersona tbody').html(trsPersona);

            $('#tblResultBuscarPersona tbody').delegate('input[type="radio"]', 'click', function (event) {
                if ($(this).prop('cheched') == true) {
                    var seccion = $(this).parent().parent().find('seccion_persona').text();

                    $('#frmBuscarPersona #seccion option[value='+seccion+']').attr('selected', true);
                }
            });
        });
    });

    $('#municipio').on('change', function () {
        $.ajax({
            url: getSeccionesMuni,
            type: 'POST',
            data: 'municipio=' + $('#municipio').val(),
            dataType: 'json',
            beforeSend: function (xhr) {
                $('#seccion').html('<option>Secciones...</option>');
                $('#seccion').after('<i class="fa fa-spinner fa-pulse fa-lg" id="loading"></i>');
            }
        }).done(function(response){
            var secciones = '<option value="">Secciones</option>';
            var seccion = 0;

            $('#loading').remove();

            for (seccion in response) {
                secciones += '<option value="' + response[seccion].NumSector+ '">' + response[seccion].NumSector+ '</option>';
            }

            $('#seccion').html(secciones);
        });
    });

});