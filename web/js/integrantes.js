$(document).ready(function (){
    'use strict';

    $('#tblIntegrantes').delegate('.btnDelIntegrante', 'click', function (){
    //$('.btnDelIntegrante').on('click', function (){
        if (confirm('¿Esta seguro que desea eliminar el integrante seleccionado?')) {
            var self = this;

            $.ajax({
                url: delIntegrante,
                method: 'POST',
                data: 'org='+idOrg+'&inte='+$(self).data('id')+'&_csrf='+yii.getCsrfToken(),
                //contentType: "application/json; charset=utf-8",
                dataType: 'json'
            }).done(function (data, textStatus, jqXHR) {
                if (data.error) {
                    alert('ERROR al eliminar el integrante');
                } else {
                    alert('Integrante eliminado exitosamente');

                    $(self).parent().parent().addClass('alert-danger');
                    $(self).parent().parent().remove();
                }
            });
        }
    });

    $('#btnAddIntegrante').on('click', function () {
        $('#modalBuscarPersona').modal('show');
    });

    $('#btnAsignarPersona').on('click', function () {
        var personaSeleccionada = $('[name=persona]:checked').val(),
            trIntegrante = '';

        if (personaSeleccionada == undefined) {
            $('#btnBuscarPersona').after('<div class="alert alert-danger" role="alert" id="alertNoPersona">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar una persona</div>');
            return false;
        }

        $('#alertNoPersona').remove();

        $.ajax({
            url: addIntegrante,
            method: 'POST',
            data: 'org='+idOrg+'&inte='+personaSeleccionada+'&_csrf='+yii.getCsrfToken(),
            //contentType: "application/json; charset=utf-8",
            dataType: 'json'
        }).done(function (data, textStatus, jqXHR) {
            if (data.error) {
                alert('ERROR al agregar el integrante');
            } else {
                alert('Integrante agregado exitosamente');

                trIntegrante = '<tr>'
                        + '<td>' + data.integrante.NombreCompleto + '</td>'
                        + '<td class="seccion">' + parseInt(data.integrante.SECCION) + '</td>'
                        + '<td>' + data.integrante.Domicilio + '</td>'
                        + '<td class="text-center"><button class="btn btn-sm btn-danger btnDelIntegrante" '+
                            'data-id="'+data.integrante.CLAVEUNICA+'" '+
                            '><i class="fa fa-user-times"></i></button></td>'
                    + '</tr>';

                $('#tblIntegrantes tbody').append(trIntegrante);
            }

            $('#modalBuscarPersona').modal('hide');
        });
    });

    $('#secciones').change(function () {
        var contador = 0;
        var $seleccionados;
        
        if ($(this).val() == 0) {
            $('.seccion').parent().show();
            $('#noIntegrantesSeccion').html('');
        } else {
            $('.seccion').parent().hide();
            $seleccionados = $('.seccion:contains(" '+$(this).val()+' ")');
            $seleccionados.parent().show();
            contador = $seleccionados.length;
            $('#noIntegrantesSeccion').html(contador + ' Integrante(s)');
        }
    });
});