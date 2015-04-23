$(document).ready(function () {
    'use strict';

    $('#personaPromovida').css('cursor', 'pointer');

    $('#personaPromovida').on('click, focus', function () {
       $('#modalBuscarPersona').modal('show');
    });

    $('#municipio_promocion').on('change', function () {
       $('#municipio option[value='+$(this).val()+']').attr('selected', true);
       $('#municipio').trigger('change');
       $('#resultValidacion').html('');
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
        $('#resultValidacion').html('');
    });

    $('#btnSendForm').click(function(event){
        if ($('#municipio_promocion').val() == 0) {
            $('#resultValidacion').html('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar un Municipio</div>');
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
        
        if ($('#promocion-idpersonapromovida').val() == '') {
            $('#resultValidacion').html('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar la Persona Promovida</div>');
            event.stopPropagation();
            event.preventDefault();
            return false;
        }

        if ($('#promocion-idpersonapromueve').val() == '') {
            $('#resultValidacion').html('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar la Persona que promueve</div>');
            event.stopPropagation();
            event.preventDefault();
            return false;
        }

        if ($('#promocion-idpersonapuesto').val() == '') {
            $('#resultValidacion').html('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar la Persona en donde promueve</div>');
            event.stopPropagation();
            event.preventDefault();
            return false;
        }

        $('#frmPromocion').trigger('submit');
    });

});