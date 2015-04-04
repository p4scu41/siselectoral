$(document).ready(function () {
    'use strict';

    $('#org-representante, #org-enlace').css('cursor', 'pointer');

    $('#org-representante, #org-enlace').on('click, focus', function () {
        $('#btnAsignarPersona').data('inputOrigen', $(this).attr('id'));
        $('#modalBuscarPersona').modal('show');
    });

    $('#btnAsignarPersona').on('click', function () {
        var personaSeleccionada = $('[name=persona]:checked').val(),
            nombrePersona = $('[name=persona]:checked').data('nombre'),
            inputOrigen = $(this).data('inputOrigen');

        if (personaSeleccionada == undefined) {
            $('#btnBuscarPersona').after('<div class="alert alert-danger" role="alert" id="alertNoPersona">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar una persona</div>');
            return false;
        } else {
            $('#alertNoPersona').remove();
        }

        $('#' + inputOrigen).val(nombrePersona);

        if (inputOrigen == 'org-representante') {
            $('#organizaciones-idpersonarepresentante').val(personaSeleccionada);
        } else if (inputOrigen == 'org-enlace') {
            $('#organizaciones-idpersonaenlace').val(personaSeleccionada);
        }

        $('#modalBuscarPersona').modal('hide');
    });
    
});