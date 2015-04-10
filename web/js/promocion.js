$(document).ready(function () {
    'use strict';

    $('#personaPromovida').css('cursor', 'pointer');

    $('#personaPromovida').on('click, focus', function () {
       $('#modalBuscarPersona').modal('show');
    });

    $('#municipio_promocion').on('change', function () {
       $('#municipio option[value='+$(this).val()+']').attr('selected', true);
       $('#municipio').trigger('change');
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

        $('#personaPromovida').val(nombrePersona);
        $('#promocion-idpersonapromovida').val(personaSeleccionada);

        $('#modalBuscarPersona').modal('hide');
    });

});