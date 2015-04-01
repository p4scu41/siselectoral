$(document).ready(function () {
    'use strict';

    $('#org-representante, #org-enlace').css('cursor', 'pointer');

    $('#org-representante, #org-enlace').on('click, focus', function () {
        $('#btnAsignarPersona').data('inputOrigen', $(this).attr('id'));
        $('#modalBuscarPersona').modal('show');
    });

    $('#btnBuscarPersona').on('click', function () {

        if ($('#municipio').val() == 0) {
            alert('Debe seleccionar un Municipio');
            return false;
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
                            'data-nombre="' + response[persona].APELLIDO_PATERNO + ' ' + response[persona].APELLIDO_MATERNO + ' ' + response[persona].NOMBRE + '"' +
                        '></td>' +
                        '<td>' + response[persona].APELLIDO_PATERNO + ' ' + response[persona].APELLIDO_MATERNO + ' ' + response[persona].NOMBRE+ '</td>' +
                        '<td>' + parseInt(response[persona].SECCION) + '</td>' +
                        '<td>' + response[persona].CASILLA + '</td>' +
                        '<td>' + response[persona].DOMICILIO + ',' + response[persona].NOM_LOC + '</td>' +
                    '</tr>';
            }

            $('#tblResultBuscarPersona tbody').html(trsPersona);
        });
    });

    $('#btnAsignarPersona').on('click', function () {
        var personaSeleccionada = $('[name=persona]:checked').val(),
            nombrePersona = $('[name=persona]:checked').data('nombre'),
            inputOrigen = $(this).data('inputOrigen');

        if (personaSeleccionada == undefined) {
            alert('Debe seleccionar una persona');
            return false;
        }

        $('#' + inputOrigen).val(nombrePersona);

        console.log(personaSeleccionada);
        console.log(nombrePersona);
        console.log(inputOrigen);

        if (inputOrigen == 'org-representante') {
            $('#organizaciones-idpersonarepresentante').val(personaSeleccionada);
        } else if (inputOrigen == 'org-enlace') {
            $('#organizaciones-idpersonaenlace').val(personaSeleccionada);
        }

        $('#modalBuscarPersona').modal('hide');
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