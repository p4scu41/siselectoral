$(document).ready(function () {
    'use strict';

    $('#personaPromovida').css('cursor', 'pointer');

    $('#personaPromovida').on('click, focus', function () {
        $('#municipio option[value='+$('#municipio_promocion').val()+']').attr('selected', true);
        $('#municipio').trigger('change');

        setTimeout(function () {
            if ($('#seccion_promocion').val() != ''){
                $('#seccion').find('option[value='+$('#seccion_promocion').val()+']').prop('selected', true);
            } 
        }, 1000)

        $('#modalBuscarPersona').modal('show');
    });

    $('#municipio_promocion').on('change', function () {
        $('#municipio option[value='+$(this).val()+']').attr('selected', true);
        $('#municipio').trigger('change');
        $('#resultValidacion').html('');

       $.ajax({
            url: getSeccionesMuni,
            type: 'POST',
            data: 'municipio=' + $('#municipio').val(),
            dataType: 'json',
            beforeSend: function (xhr) {
                $('#seccion_promocion').html('<option>Secciones...</option>');
                $('#seccion_promocion').after('<i class="fa fa-spinner fa-pulse fa-lg" id="loading"></i>');
            }
        }).done(function(response){
            var secciones = '<option value="">Secciones</option>';
            var seccion = 0;

            $('#loading').remove();

            for (seccion in response) {
                secciones += '<option value="' + response[seccion].NumSector+ '">' + response[seccion].NumSector+ '</option>';
            }

            $('#seccion_promocion').html(secciones);
        });
    });

    $('#seccion_promocion').change(function(event){
        $('#seccion option[value='+$(this).val()+']').attr('selected', true);
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