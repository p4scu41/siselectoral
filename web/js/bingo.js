$(document).ready(function(){
    $('#municipio').change(function(){
        $('#loadIndicator').show();
        $('#alertResult').html('');
        
        $.ajax({
            url: getSeccionesMuni,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&municipio=' + $('#municipio').val(),
            dataType: 'json',
        }).done(function(response){
            var secciones = ''+
                '<label for="list-jefe-de-seccion">Secciones: </label>'+
                '<select id="list-jefe-de-seccion" class="form-control" name="seccion">'+
                    '<option value="0">Jefes de Sección</option>';

            for (seccion in response) {
                secciones += '<option value="' + response[seccion].IdNodoEstructuraMov+ '" data-nivel="5">'+response[seccion].NumSector+' '+response[seccion].NOMBRECOMPLETO+'</option>';
            }

            secciones += '</select>';

            $('#listJefeSeccion').html(secciones);
            $('#loadIndicator').hide();

            $('#list-jefe-de-seccion').change(function(){
                $('#alertResult').html('');
                self = this;

                if ($(self).val() == 0) {
                    $('#municipio').trigger('change');
                    return false;
                }

                $('#loadIndicator').show();
                
                $.ajax({
                    url: getAvance,
                    type: 'POST',
                    data: '_csrf='+$('[name=_csrf]').val()+'&idNodo=' + $(self).val(),
                    dataType: 'json',
                }).done(function(response){
                    $('#loadIndicator').hide();
                    $('#resumen_promocion').html('');

                    if (response != 'null') {
                        $('#resumen_promocion').html('<div class="alert alert-success" role="alert">'+
                            '<p>Contacto del Jefe de Sección. Tel. Móvil: '+response.TELMOVIL+'</p>'+
                            '<strong>Meta: </strong> '+response.Meta+
                            ' &nbsp; &rarr; &nbsp; <strong>Avance: </strong> '+response.Promovidos+
                            ' &nbsp; &rarr; &nbsp; <strong>% Avance: </strong> '+response.Avance+
                            ' &nbsp; &rarr; &nbsp; <strong>En espera: </strong> '+(parseInt(response.Meta) - parseInt(response.Promovidos))+
                          '</div>');
                    }
                });
            });
        });
        
        $.ajax({
            url: getAvance,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&municipio=' + $('#municipio').val(),
            dataType: 'json',
        }).done(function(response){
            $('#resumen_promocion').html('');
            if (response != 'null') {
                $('#resumen_promocion').html('<div class="alert alert-success" role="alert">'+
                    '<p>Contacto del Coordinador Municipal. Tel. Móvil: '+response.TELMOVIL+'</p>'+
                    '<strong>Meta: </strong> '+response.Meta+
                    ' &nbsp; &rarr; &nbsp; <strong>Avance: </strong> '+response.Promovidos+
                    ' &nbsp; &rarr; &nbsp; <strong>% Avance: </strong> '+response.Avance+
                    ' &nbsp; &rarr; &nbsp; <strong>En espera: </strong> '+(parseInt(response.Meta) - parseInt(response.Promovidos))+
                  '</div>');
            }
        });

    });

    $('#btnBuscar').click(function(){
        if ($('#municipio').val() == 0 || typeof($('#municipio').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Municipio</div>');
            return false;
        }

        if ($('#list-jefe-de-seccion').val() == 0 || typeof($('#list-jefe-de-seccion').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Jefe de Sección</div>');
            return false;
        }

        $('#alertResult').html('');
        $('#bingoListPromovidos').html('');
        $('#loadIndicator').show();

        $.ajax({
            url: getPromotoresBySeccion,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&nodo=' + $('#list-jefe-de-seccion').val(),
            dataType: 'json'
        }).done(function(response){
            if (response.length == 0) {
                $('#itemsBingo').html('<div class="jumbotron"><h1 class="text-center">Bingo!!!!</h1></div>');
                $('#bingoListPromotores').html('');
                $('#modalBingo .modal-title').html('Bingo - Sección '+$('#list-jefe-de-seccion option:selected').text());
                $('#modalBingo').modal('show');
            } else {
                listPromotores = '<ul class="list-group">';
                totalFaltantes = 0;

                for (promotor in response) {
                    listPromotores += '<li class="list-group-item" data-id="'+response[promotor].idNodo+'"><span class="badge">'+
                        response[promotor].faltantes+'</span>'+response[promotor].NOMBRECOMPLETO+'</li>';
                    totalFaltantes += parseInt(response[promotor].faltantes);
                }

                listPromotores += '<li class="list-group-item disabled" data-id="0"> <span class="pull-rigth">Total</span> <span class="badge">'+totalFaltantes+'</span></li></ul>';
                $('#bingoListPromotores').html(listPromotores);
            }

            $('#loadIndicator').hide();
        });
    });

    $('#bingoListPromotores').delegate('.list-group-item', 'click', function(){
        $('.list-group-item').removeClass('active');

        if ($(this).data('id') == 0) {
            return false;
        }

        $(this).addClass('active');
        $('#loadIndicator').show();
        self = this;

        $.ajax({
            url: getInfoPromo,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&id=' + $(self).data('id'),
            dataType: 'json'
        }).done(function(response){
            if (response.length == 0) {
                $('#alertResult').html('<div class="alert alert-danger">No se encontró al Promotor</div>');
                $('#infoPromotor').html('');
            } else {
                promotor = '<div class="media">'+
                        '<div class="media-body">'+
                            '<h5 class="media-heading"><label class="">' +
                            response.NOMBRECOMPLETO+'</label></h5>' +
                            '<p>Domicilio: ' + response.DOMICILIO + '<p>' +
                            '<p>Código Postal: ' + parseInt(response.CODIGO_POSTAL) + '<p>' +
                            '<p>Colonia: ' + response.COLONIA + '<p>' +
                            '<p>Tel. Móvil: ' + response.TELMOVIL + '<p>' +
                            //'<p>Tel. Casa: ' + response.TELCASA + '<p>' +
                        '</div>'+
                    '</div>';
                $('#infoPromotor').html(promotor);
            }

            $('#loadIndicator').hide();
        });
    });

    $('#btnGenerarBingo').click(function(){
        if ($('#municipio').val() == 0 || typeof($('#municipio').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Municipio</div>');
            return false;
        }

        if ($('#list-jefe-de-seccion').val() == 0 || typeof($('#list-jefe-de-seccion').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Jefe de Sección</div>');
            return false;
        }

        $('#btnBuscar').trigger('click');
        $('#alertResult').html('');
        $('#loadIndicator').show();

        $.ajax({
            url: getPromovidosBySeccion,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&nodoseccion=' + $('#list-jefe-de-seccion').val(),
            dataType: 'json'
        }).done(function(response){
            contador = 1;
            bingo = true;
            $('#itemsBingo').html('');

            for (promovido in response) {
                if (response[promovido].Participacion == null) {
                    item = '<li class="liBingo" title="'+response[promovido].PROMOTOR+'">'+
                        '<span class="glyphicon">'+contador+'</span>'+
                        '<span class="glyphicon-class"><input type="checkbox" name="chk_promovido" value="'+response[promovido].CLAVEUNICA+'"></span>'+
                    '</li>';
                    $('#itemsBingo').append(item);
                    bingo = false;
                }

                contador++;
            }

             $(".liBingo").tooltip({
                position: {
                  my: "center bottom-20",
                  at: "center top",
                  using: function( position, feedback ) {
                    $( this ).css( position );
                    $( "<div>" )
                      .addClass( "arrow" )
                      .addClass( feedback.vertical )
                      .addClass( feedback.horizontal )
                      .appendTo( this );
                  }
                }
              });

            if (bingo) {
                $('#itemsBingo').html('<div class="jumbotron"><h1 class="text-center">Bingo!!!!</h1></div>');
            }

            $('#loadIndicator').hide();
            $('#modalBingo .modal-title').html('Bingo - Sección '+$('#list-jefe-de-seccion option:selected').text());
            $('#modalBingo').modal('show');
        });
    });

    $('#itemsBingo').delegate('li', 'click', function(event){
        event.stopPropagation();
        
        $(this).find('input[type="checkbox"]').trigger('click');

        if ($(this).find('input[type="checkbox"]').prop('checked')) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });

    $('#btnAceptarBingo').click(function(){
        listPromovidos = $('#itemsBingo input[type="checkbox"]:checked');
        promovidos = '';
        participacion = 0;

        if (listPromovidos.length != 0) {
            listPromovidos.each(function(){
                promovidos += $(this).val()+'|';
                participacion++;
            });

            $('#loadIndicator').show();
            $('#loadSetBingo').show();
            $.ajax({
                url: setParticipacion,
                type: 'POST',
                data: '_csrf='+$('[name=_csrf]').val()+'&promovidos=' + promovidos,
                dataType: 'json',
            }).done(function(response){
                $('#btnGenerarBingo').trigger('click');
                $('#list-jefe-de-seccion').trigger('change');
                $('#loadIndicator').hide();
                $('#loadSetBingo').hide();

                restante = parseInt($('.list-group-item.active').find('.badge').text()) - participacion;

                if (restante == 0) {
                    $('.list-group-item.active').remove();
                } else {
                    $('.list-group-item.active').find('.badge').text(restante);
                }
            });
        }
    });

    $('#btnGenerarListado').click(function(event){
        event.stopPropagation();
        event.preventDefault();

        if ($('#municipio').val() == 0 || typeof($('#municipio').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Municipio</div>');
            return false;
        }
        
        if ($('#list-jefe-de-seccion').val() == 0 || typeof($('#list-jefe-de-seccion').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Jefe de Sección</div>');
            return false;
        }
        
        $('#alertResult').html('');
        $('#loadIndicator').show();

        $.ajax({
            url: getPromovidosBySeccion,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&nodoseccion=' + $('#list-jefe-de-seccion').val(),
            dataType: 'json'
        }).done(function(response){
            $('#modalListado .modal-body').html('<ol></ol>');

            if ( !$('#modalListado').hasClass('modal-wide') ) {
                $('#modalListado').addClass('modal-wide');
            }
            letraActual = '';

            for (item in response) {
                if (letraActual != response[item].Letra) {
                    $('#modalListado .modal-body ol').append('<h4>'+response[item].Letra+'</h4>');
                }
                letraActual = response[item].Letra;

                li = '<li>'+response[item].NOMBRECOMPLETO+'</li>';
                $('#modalListado .modal-body ol').append(li);
            }

            $.fileDownload(urlReportepdf, {
                //'prepareCallback': function(url){ $('body').prepend('<div class="loading"></div>'); },
                successCallback: function(url){ $('#loadIndicator').hide(); },
                failCallback: function(responseHtml, url){ $('#alertResult').html('<div class="alert alert-danger">Error al intentar descargar el archivo.</div>'); },
                httpMethod: "POST",
                data: {
                    'title': 'Listado de promovidos de la Sección '+$('#list-jefe-de-seccion option:selected').text(),
                    'content': $('#modalListado .modal-body').html(),
                    'columns': 3,
                    '_csrf': $('[name=_csrf]').val(),
                }
            }).done(function () { $('#loadIndicator').hide(); });
            
            setTimeout(function(){$('#loadIndicator').hide();}, 19000);
            //$('#modalListado').modal('show');
            
        });
    });

    $('#btnVerFaltantes').click(function(){
        if ($('#municipio').val() == 0 || typeof($('#municipio').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Municipio</div>');
            return false;
        }

        if ($('#list-jefe-de-seccion').val() == 0 || typeof($('#list-jefe-de-seccion').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Jefe de Sección</div>');
            return false;
        }
        
        if ($('.list-group-item.active').length == 0) {
            $('#infoPromotor').html('<div class="alert alert-danger">Debe seleccionar un Promotor</div>');
            return false;
        }

        if ( !$('#modalListado').hasClass('modal-wide') ) {
            $('#modalListado').addClass('modal-wide');
        }

        $.ajax({
            url: getPromovidosByPromotor,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&promotor=' + $('.list-group-item.active').data('id'),
            dataType: 'json'
        }).done(function(response){
            $('#modalListado .modal-body').html('<ul></ul>');

            for (item in response) {
                if (response[item].Participacion == null) {
                    li = '<li>'+response[item].NOMBRECOMPLETO+'</li>';

                    $('#modalListado .modal-body ul').append(li);
                }
            }

            $('#modalListado').modal('show');
            $('#loadIndicator').hide();
        });
    });

    $('#btnVerRCs').click(function(){
        if ($('#municipio').val() == 0 || typeof($('#municipio').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Municipio</div>');
            return false;
        }

        if ($('#list-jefe-de-seccion').val() == 0 || typeof($('#list-jefe-de-seccion').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Jefe de Sección</div>');
            return false;
        }

        $('#modalListado').removeClass('modal-wide');

        $.ajax({
            url: getRCFromSeccion,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&nodo=' + $('#list-jefe-de-seccion').val(),
            dataType: 'json'
        }).done(function(response){
            $('#modalListado .modal-body').html('');

            tabla = '<table class="table table-condensed table-striped table-bordered table-hover">'+
                '<thead><tr><th>Casilla</th><th>Representante</th><th>Tel.</th></tr></thead>'+
                '<tbody>';

            for (item in response) {
                if (response[item].Participacion == null) {
                    tabla += '<tr><td>'+response[item].casilla+'</td><td>'+
                        response[item].representante+'</td><td>'+
                        response[item].tel+'</td></tr>';
                }
            }

            tabla += '<tbody></table>';
            $('#modalListado .modal-body').html(tabla);

            $('#modalListado').modal('show');
            $('#loadIndicator').hide();
        });
    });

    /*$('#bingoListPromovidos').delegate('input[type="checkbox"]', 'click', function(){
        if ($(this).prop('checked')) {
            console.log($(this).data('id'));
            $(this).closest('.media').fadeOut('slow', function(){ $(this).remove(); });
        }
    });*/

    /*$('#btnSeleccionarTodos').click(function(){
        $('#bingoListPromovidos input[type="checkbox"]').prop('checked', true);
    });

    $('#btnParticipar').click(function(){
        listPromovidos = $('#bingoListPromovidos input[type="checkbox"]:checked');
        promovidos = '';

        listPromovidos.each(function(){
            promovidos += $(this).val()+'|';
        });

        $('#loadIndicator').show();
        $.ajax({
            url: setParticipacion,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&promovidos=' + promovidos,
            dataType: 'json',
        }).done(function(response){
            $('#btnBuscar').trigger('click');
            $('#bingoListPromovidos').html('');
        });
    });*/

    $('#btnImprimirBingo').click(function(){
        $imprimible = $('<div class="box box-primary box-success"><div class="panel panel-success" id="containerPerson" style="margin-bottom: 1px !important;">'+
                '<div class="panel-body">'+
                '<h4 class="text-center">'+$('#modalBingo .modal-title').html()+'</h4>'+
                ' '+ $('#modalBingo .modal-body').html()+
                '</div></div></div>');

        //console.log($imprimible.html());

        $($imprimible).printArea({"mode":"popup","popClose":true});
    });

    $('#btnStatusSecciones').click(function(){
        if ($('#municipio').val() == 0 || typeof($('#municipio').val()) == 'undefined') {
            $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar un Municipio</div>');
            return false;
        }

        $('#alertResult').html('');
        $('#loadIndicator').show();

        $.ajax({
            url: urlStatussecciones,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&muni=' + $('#municipio').val(),
            dataType: 'json'
        }).done(function(response){
            $('#listStatusSecciones').html('');

            for (seccion in response) {
                item = '<li class="liBingo '+(response[seccion].participacionFaltantes==0 ? '' : 'red')+'">'+
                    '<span class="glyphicon">'+response[seccion].NumSector+'</span>'+
                    '<span class="glyphicon-class">'+response[seccion].participacionFaltantes+'</span>'+
                '</li>';
                $('#listStatusSecciones').append(item);
            }

            $('#loadIndicator').hide();
            $('#modalStatusSecciones').modal('show');
        });
    });

});
