$(document).ready(function (){
    'use strict';

    $('#tblIntegrantes').delegate('.btnDelIntegrante', 'click', function (){
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
                        + '<td>' + data.integrante.DescMunicipio + '</td>'
                        + '<td class="text-center"><a href="#" class="promovidoIntegrante" data-id="'+data.integrante.CLAVEUNICA+'" data-promotor=""><i class="fa fa-square-o fa-lg"></i></a></td>'
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
        var filtro = $(this).select2('val');
        var contador = 0;
        var $seleccionados;
        
        if (filtro.length == 0) {
            $('.seccion').parent().show();
            $('#noIntegrantesSeccion').html('');
        } else {
            $('.seccion').parent().hide();
            var seccion = 0;

            for(seccion in filtro) {
                $seleccionados = $('.seccion:contains(" '+filtro[seccion]+' ")');
                $seleccionados.parent().show();
                contador += $seleccionados.length;
            }
            $('#noIntegrantesSeccion').html(contador + ' Integrante(s)');
        }
    });

    if ($('#tblIntegrantes tbody tr').length != 0) {
        $('.opcionesExportar').show();
    }

    var $form = $('<form action="" method="post" target="_blank">'+
            '<input type="text" name="title" id="title">'+
            '<textarea name="content" id="content"></textarea>'+
            '<input type="hidden" name="_csrf" value="'+yii.getCsrfToken()+'"></form>');

    $('.btnExportPdf, .btnExportExcel').click(function(event){
        var thead = $('#tblIntegrantes thead').clone();
        thead.find('tr > th:last').remove();

        var tbody = $('#tblIntegrantes tbody tr:visible').clone();
        tbody.find('button').parent().remove();

        var content = '<table class="table table-condensed table-striped table-bordered table-hover">'+
            '<thead>'+thead.html()+'</thead>'+
            '<tbody>'+$.map( tbody, function (element) { return '<tr>'+$(element).html()+'</tr>' }).join(' ')+'</tbody>'+
            '</table>';

        if ($(this).hasClass('btnExportExcel')) {
            //http://jsfiddle.net/terryyounghk/KPEGU/
            var $rows = $(content).find('tr:has(th), tr:has(td)'),

            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character

            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"',

            // Grab text from table into CSV formatted string
            content = '"' + $rows.map(function (i, row) {
                var $row = $(row),
                    $cols = $row.find('th, td');

                return $cols.map(function (j, col) {
                    var $col = $(col),
                        text = $col.text();

                    return text.replace('"', '""'); // escape double quotes

                }).get().join(tmpColDelim);

            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"';
        }

        $form.find('#content').text( content );
        $form.find('#title').val( $('#titulo').html() );
        $form.attr('action', $(this).data('url'));
        $('#formExport').html($form);
        $form.submit();

        event.stopPropagation();
        event.preventDefault();
        return false;
    });
    
    $('#tblIntegrantes tbody').delegate('.promovidoIntegrante', 'click', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var id = $(this).data('id');
        var promotor = $(this).data('promotor');

        if (promotor == '') {
            $('#dialog').html('<div class="alert alert-danger" role="alert" style="padding-left: 0px; margin-left: 0px;">'+
                'El integrante seleccionado no esta promovido</div>');
            $('#dialog').dialog('open');
        } else {
            $.ajax({
                url: getPromotores,
                method: 'POST',
                dataType: 'json',
                data: {id: id}
            }).done(function (response) {
                var msgDialog = '<div class="alert alert-success" role="alert" style="padding-left: 0px; margin-left: 0px;">'+
                    'El integrante seleccionado esta promovido por: <ul>';
                var p = 0;

                for (p in response) {
                    msgDialog += '<li>'+response[p].NombreCompleto+'</li>';
                }

                msgDialog += '<ul></div>';

                $('#dialog').html(msgDialog);
                $('#dialog').dialog('open');
            });

        }
    });

    $('#dialog').dialog({autoOpen: false});
});