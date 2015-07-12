$(document).ready(function () {
    'use strict';

    $('#persona').css('cursor', 'pointer');

    $('#persona').on('click, focus', function () {
       $('#modalBuscarPersona').modal('show');
    });

    $('#btnAsignarPersona').on('click', function () {
        var personaSeleccionada = $('[name=persona]:checked').val(),
            nombrePersona = $('[name=persona]:checked').data('nombre');

        if (personaSeleccionada == undefined) {
            $('#btnBuscarPersona').after('<div class="alert alert-danger" role="alert" id="alertNoPersona">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar una persona</div>');
            return false;
        } else {
            $('#alertNoPersona').remove();
        }

        $('#persona').val(nombrePersona);
        $('#usuarios-idpersona').val(personaSeleccionada);

        $('#modalBuscarPersona').modal('hide');
    });

});