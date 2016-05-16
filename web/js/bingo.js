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
            var zonas = new Array();
            var selectZonas = '<label for="list-zonas">Zonas: </label>'+
                '<select id="list-zonas" class="form-control" name="zona">'+
                    '<option value="0">Zonas</option>';
            var secciones = '<label for="list-jefe-de-seccion">Secciones: </label>'+
                '<select id="list-jefe-de-seccion" class="form-control" name="seccion">'+
                    '<option value="0">Jefes de Sección</option>';

            for (seccion in response) {
                secciones += '<option value="' + response[seccion].IdNodoEstructuraMov+ '" data-nivel="5" data-zona="'+response[seccion].ZonaMunicipal+'">'+response[seccion].NumSector+' '+response[seccion].NOMBRECOMPLETO+'</option>';
                zonas[response[seccion].ZonaMunicipal] = response[seccion].ZonaMunicipal;
            }

            for (iZona in zonas) {
                selectZonas += '<option value="' + zonas[iZona]+ '">'+zonas[iZona]+'</option>';
            }

            secciones += '</select>';
            selectZonas += '</select>';

            $('#listJefeSeccion').html(secciones);
            $('#listZonas').html(selectZonas);
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
                        $('#resumen_promocion').html('<div class="alert alert-success" role="alert"><div class="row">'+
                            '<div class="col-md-6">'+
                            '<p>Contacto del Jefe de Sección:</p>'+
                            '<ul>'+
                            '<li>Domicilio: '+response.DOMICILIO+'</li>'+
                            '<li>Código Postal: '+response.CODIGO_POSTAL+'</li>'+
                            '<li>Tel. Móvil: '+response.TELMOVIL+'</li>'+
                            '</ul>'+
                            '<strong>Meta: </strong> '+response.Meta+
                            ' &nbsp; &rarr; &nbsp; <strong>Avance: </strong> '+response.Promovidos+
                            ' &nbsp; &rarr; &nbsp; <strong>% Avance: </strong> '+response.Avance+
                            ' &nbsp; &rarr; &nbsp; <strong>En espera: </strong> '+(parseInt(response.Meta) - parseInt(response.Promovidos))+
                            '</div>'+
                            '<div class="col-md-6"><strong>AVANCE DE BINGO</strong><br>'+
                            '<strong>Zona: </strong> '+response.zona+
                            ' &nbsp; &rarr; &nbsp; <strong>Meta: </strong> '+response.MetaZona+
                            ' &nbsp; &rarr; &nbsp; <strong>Avance: </strong> '+response.PromovidosZona+
                            ' &nbsp; &rarr; &nbsp; <strong>% Avance: </strong> '+response.AvanceZona+
                            ' &nbsp; &rarr; &nbsp; <strong>En espera: </strong> '+(parseInt(response.MetaZona) - parseInt(response.PromovidosZona))+
                            '</div>'+
                          '</div></div>');
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
                $('#modalBingo .modal-title').html('Bingo - Zona '+$('#list-jefe-de-seccion option:selected').data('zona')+' Sección '+$('#list-jefe-de-seccion option:selected').text());
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
                            '<h5 class="media-heading"><label id="activista">' +
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
        $('#itemsBingo').html('<i class="fa fa-refresh fa-spin" style="display: none; font-size: x-large;"></i>')

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
                    item = '<li class="liBingo tooltip" title="Promovido '+response[promovido].NOMBRECOMPLETO+'<br>'+response[promovido].PROMOTOR+' ACT SEC '+response[promovido].IdSector+' Z'+response[promovido].ZonaMunicipal+'">'+
                        '<span class="glyphicon">'+contador+'</span>'+
                        '<span class="glyphicon-class"><input type="checkbox" name="chk_promovido" value="'+response[promovido].CLAVEUNICA+'"></span>'+
                    '</li>';
                    $('#itemsBingo').append(item);
                    bingo = false;
                }

                contador++;
            }

            $('.tooltip').tooltipster({
                contentAsHTML: true
            });

             /*$(".liBingo").tooltip({
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
              });*/

            if (bingo) {
                $('#itemsBingo').html('<div class="jumbotron"><h1 class="text-center">Bingo!!!!</h1></div>');
            }

            $('#loadIndicator').hide();
            $('#modalBingo .modal-title').html('Bingo - Zona '+$('#list-jefe-de-seccion option:selected').data('zona')+' Sección '+$('#list-jefe-de-seccion option:selected').text());
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
                    'title': 'Listado de promovidos de la Zona '+$('#list-jefe-de-seccion option:selected').data('zona')+' Sección '+$('#list-jefe-de-seccion option:selected').text(),
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

        $('#modalListado').removeClass('modal-wide');

        $.ajax({
            url: getPromovidosByPromotor,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&promotor=' + $('.list-group-item.active').data('id'),
            dataType: 'json'
        }).done(function(response){
            var seccion = $('#list-jefe-de-seccion option:selected').text().split(' ');
            $('#modalListado .modal-body').html('<ul></ul>');
            $('#modalListado .modal-title').html('Zona '+$('#list-jefe-de-seccion option:selected').data('zona')+' Sección '+seccion[0]+' Activista '+$('#activista').text());

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
            $('#modalListado .modal-title').html('Representantes de Casilla Zona '+$('#list-jefe-de-seccion option:selected').data('zona')+' Sección '+$('#list-jefe-de-seccion option:selected').text());

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
            tablaStatusSecciones = '<table class="table table-condensed table-striped table-bordered table-hover">'+
                '<thead><tr><th>Zona</th><th>Seccion</th><th>Total Promovidos</th><th>Total Participacion</th></tr></thead>'+
                '<tbody>';
            sumaTotal = 0;
            sumaZona = 0;
            zonaActual = 0;

            for (seccion in response) {
                sumaTotal += (parseInt(response[seccion].participacionFaltantes)+parseInt(response[seccion].participacionEfectivos));
                sumaZona += (parseInt(response[seccion].participacionFaltantes)+parseInt(response[seccion].participacionEfectivos));

                tablaStatusSecciones += '<tr><td>'+response[seccion].ZonaMunicipal+
                    '</td><td>'+response[seccion].NumSector+
                    '</td><td>'+(parseInt(response[seccion].participacionFaltantes)+parseInt(response[seccion].participacionEfectivos))+
                    '</td><td>'+response[seccion].participacionEfectivos+'</td></tr>';

                item = '<li class="liBingo tooltip '+(response[seccion].participacionFaltantes==0 ? '' : 'red')+'" title="Z'+response[seccion].ZonaMunicipal+' '+response[seccion].coordZona+'">'+
                    '<span class="glyphicon">'+response[seccion].NumSector+'</span>'+
                    '<span class="glyphicon-class">'+response[seccion].participacionEfectivos+' / '+response[seccion].participacionFaltantes+'</span>'+
                '</li>';

                $('#listStatusSecciones').append(item);
            }

            tablaStatusSecciones += '</tbody></tabla>';

            //http://jsfiddle.net/terryyounghk/KPEGU/
            var $rows = $(tablaStatusSecciones).find('tr:has(th), tr:has(td)'),

            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character

            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"',

            // Grab text from table into CSV formatted string
            tablaCSV = '"' + $rows.map(function (i, row) {
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

            $('#content').val(tablaCSV);

            $('.tooltip').tooltipster({
                contentAsHTML: true
            });

            $('#loadIndicator').hide();
            $('#modalStatusSecciones').modal('show');
        });
    });

    $('#btnImprimirStatusSecciones').click(function(event){
        $('#frmSendStatusSecciones').trigger('submit');
        /*$imprimible = $('<div class="box box-primary box-success"><div class="panel panel-success" id="containerPerson" style="margin-bottom: 1px !important;">'+
                '<div class="panel-body">'+
                '<h4 class="text-center">'+$('#tblStatusSecciones').val()+'</h4>'+
                ' '+ $('#modalBingo .modal-body').html()+
                '</div></div></div>');

        $($imprimible).printArea({"mode":"popup","popClose":true});*/
    });

    /*
     * Pantalla Windows 730 Document 780
     * Lap Lopez Juan Windows 947 Document 997
     **/
    if ($(window).height() > 940) {
        $('.modal-wide').each(function(){
            $(this).find('.modal-body').css('height', '780px');
            $(this).find('#listStatusSecciones').css('max-height', '750px');
            $(this).find('#itemsBingo').css('max-height', '750px');
        });
    } else if ($(window).height() > 720) {
        $('.modal-wide').each(function(){
            $(this).find('.modal-body').css('height', '555px');
            $(this).find('#listStatusSecciones').css('max-height', '515px');
            $(this).find('#itemsBingo').css('max-height', '515px');
        });
    }

    $('#listZonas').on('change', 'select', function(event) {
        event.preventDefault();
        zona = $(this).val();
        
        $('#list-jefe-de-seccion option[data-zona="'+zona+'"]').show();
        $('#list-jefe-de-seccion option:not([data-zona="'+zona+'"])').hide();
        $('#list-jefe-de-seccion option:first').show();
    });

});
