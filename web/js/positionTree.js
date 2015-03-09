$(document).ready(function(){
    var CookieParams = null;
    var btnAsignarPersona = $('<div class="text-center"><button type="button" class="btn btn-success" id="btnAsignarPersona"><i class="fa fa-user-plus"></i> Asignar persona</button></div>')

    $('#btnSaveAsignaPersona').click(function(event){
        //Asignar persona al puesto
        $.ajax({
            url: urlAsignarPersona,
            dataType: "json",
            data: {claveunica: $('[name=personaSeleccionada]:checked').val(), nodo: btnAsignarPersona.find('button').data('idNodo')},
            type: "GET",
        }).done(function(response){
            if (response.error) {
                alert('Ocurrió un error al realizar la asignación del puesto.');
            } else {
                $.alert('Persona asignada al puesto exitosamente.', {
                    title: 'Asignación Exitosa',
                    buttons: [
                        {
                            title: 'Ok',
                            callback: function() { $('#btnBuscar').click(); $(this).dialog("close"); },
                        }
                    ]
                });
            }
        });

        $('#modalAsignaPersona').modal('hide');
    });

    $('#btnBuscarPersona').click(function(event){
        $filtrosBuscar = {
            "PadronGlobalSearch":{
                "MUNICIPIO": $('#MUNICIPIO_persona').val(),
                "APELLIDO_PATERNO": $('#APELLIDO_PATERNO').val(),
                "APELLIDO_MATERNO": $('#APELLIDO_MATERNO').val(),
                "NOMBRE": $('#NOMBRE').val()
            }
        };

        $('#resultBuscarPersona').html('<div class="text-center"><i class="fa fa-refresh fa-spin" style="font-size: x-large;"></i></div>');

        $.ajax({
            url: urlBuscarPersona,
            dataType: "json",
            data: $filtrosBuscar,
            type: "GET",
        }).done(function(response){
            $tabla = '<table border="1" cellpadding="1" cellspacing="1" class="table table-condensed table-striped table-bordered table-hover">'+
            '<thead>'+
                '<tr>'+
                    '<th></th>'+
                    '<th>Apellido Paterno</th>'+
                    '<th>Apellido Materno</th>'+
                    '<th>Nombre(s)</th>'+
                    '<th>Fecha Nacimiento</th>'+
                    '<th>Sección</th>'+
                    '<th>Casilla</th>'+
                '</tr>'+
            '</thead>'+
            '<tbody>';

            for(persona in response) {
                $tabla += '<tr>'+
                    '<td class="text-center"><input type="radio" name="personaSeleccionada" value="'+response[persona].CLAVEUNICA+'"></td>'+
                    '<td>'+response[persona].APELLIDO_PATERNO+'</td>'+
                    '<td>'+response[persona].APELLIDO_MATERNO+'</td>'+
                    '<td>'+response[persona].NOMBRE+'</td>'+
                    '<td>'+response[persona].FECHANACIMIENTO+'</td>'+
                    '<td>'+response[persona].SECCION+'</td>'+
                    '<td>'+response[persona].CASILLA+'</td>'+
                '</tr>';
            }

            $tabla += '</tbody></table>';

            $('#resultBuscarPersona').html($tabla);
        });
    });

    if ($.cookie('parametros')) {
        CookieParams = $.deparam($.cookie('parametros'));

        $('#btnResumen').show();
        $('#municipio option[value='+parseInt(CookieParams.Municipio)+']').attr('selected', true);
        $('#puesto option[value='+parseInt(CookieParams.IdPuesto)+']').attr('selected', true);
    }

    var verModalNodo = function(e, nodoKey){
        $('#loadIndicator').show();
        var node = null;
        if (typeof nodoKey == 'undefined') {
            node = $.ui.fancytree.getNode(e);
            e.stopPropagation();  // prevent fancytree activate for this row
            e.preventDefault();
        } else {
            node = $("#treeContainer").fancytree("getActiveNode", nodoKey)//$("#treeContainer").fancytree("getNodeByKey", nodoKey);//tree.getNodeByKey(nodoKey);
        }
        //var $button = $(e.target);

        if($('#alertDescDependiente').length != 0) {
            $('#alertDescDependiente').remove();
        }

        //console.log(node.data.IdPuesto);

        if (node.data.persona == '00000000-0000-0000-0000-000000000000') {
            $('#loadIndicator').hide();
            $('#titulo_puesto').html(node.title.replace(' - ', '<br>').replace(/\[\d+\]/,''));
            $('#imgPerson').attr('src', imgNoPerson);
            $('#frmPersonDetails').html('<div class="alert alert-danger"><i class="fa fa-frown-o fa-lg"></i> Puesto no asignado</div>');

            // pl = Puesto Login
            // Solo puede asignar puesto de promotor
            if (node.data.IdPuesto == 7) {
                // El usuario logueado solo puede asignar puestos a sus estructura inferior
                if( $('#frmPersonDetails #btnAsignarPersona').length == 0) {
                    $('#frmPersonDetails').append(btnAsignarPersona);
                }

                btnAsignarPersona.find('button').data('idNodo', node.key);

                btnAsignarPersona.delegate('button','click', function(event){
                    $('#modalPerson').modal('hide');
                    $('#resultBuscarPersona').html('');
                    $('#modalAsignaPersona').modal('show');
                });
            }

            $('#modalPerson').modal('show');
            $('#btnViewPerson').data('id', '#');
        } else {
            var fecha = new Date();
            $('#fechaResumenNodo').html('Fecha de corte: '+padLeft(fecha.getDate(),2)+'-'+padLeft((fecha.getMonth()+1),2)+'-'+fecha.getFullYear());

            $.ajax({
                url: urlPerson,
                dataType: "json",
                data: {id: node.data.persona},
                type: "GET",
            }).done(function(response) {
                $nombreCompleto = response.APELLIDO_PATERNO+' '+
                                  response.APELLIDO_MATERNO+' '+
                                  response.NOMBRE;
                var sexo = 'U';

                $datos = [
                    {'colum': 'CALLE', 'label': 'Domicilio'},
                    {'colum': 'CORREOELECTRONICO', 'label': 'E-mail'},
                    {'colum': 'TELMOVIL', 'label': 'Tel. Móvil'},
                    {'colum': 'SECCION', 'label': 'Sección'},
                    {'colum': 'CASILLA', 'label': 'Casilla'},
                    //{'colum': 'DISTRITO', 'label': 'Distrito'},
                ];

                $tplFila = '<div class="form-group">'+
                    '<label class="col-sm-3 col-xs-2 control-label">Nombre</label>'+
                    '<div class="col-sm-9 col-xs-10">'+
                        '<div class="well well-sm">'+$nombreCompleto+'</div>'+
                    '</div>'+
                '</div>';

                $('#titulo_puesto').html(node.title.replace(' - ', '<br>').replace(/\[\d+\]/,''));
                $('#frmPersonDetails').html('');
                $('#frmPersonDetails').append($tplFila);

                for($fila in $datos) {
                    var valor = response[$datos[$fila].colum];
                    valor = (valor == null ? '' : valor);
                    valor = (valor == '' ? '&nbsp;' : valor);

                    if($datos[$fila].colum == 'SEXO') {
                        sexo = valor;
                    }

                    $tplFila = '<div class="form-group">'+
                        '<label class="col-sm-3 col-xs-2 control-label">'+$datos[$fila].label+'</label>'+
                        '<div class="col-sm-9 col-xs-10">'+
                            '<div class="well well-sm">'+valor+'</div>'+
                        '</div>'+
                    '</div>';

                    $('#frmPersonDetails').append($tplFila);
                }

                if (sexo == '') {
                    sexo = 'U';
                }
                $('#imgPerson').attr('src', response.foto);

                $('#btnViewPerson').data('id', node.data.persona);

                $('#loadIndicator').hide();
                $('#modalPerson').modal('show');
            });
        }

        $.ajax({
            url: urlResumenNodo,
            dataType: "json",
            data: {idNodo: node.key},
            type: "GET",
        }).done(function(response) {
            if(response) {
                meta = response[ response.length-2 ];
                $('#no_meta').html(meta['Avances %']+'%');

                tablaResumenNodo = $(ConvertJsonToTable(response, 'tablaResumen', 'table table-condensed table-striped table-bordered table-hover', 'Download'));

                //tablaResumenNodo.find('tr:last').addClass('itemHide');
                //tablaResumenNodo.find('tr:last').prev().replaceWith('<tr><td colspan="5">AVANCE DE LA META DE PROMOCIÓN CIUDADA</td></tr>');

                tablaPromocion = $('<div class="table-responsive"><table class="table table-condensed table-bordered table-hover" id="tablaPromocion">'+
                                    '<tr><td colspan="5"><strong>AVANCE DE LA META DE PROMOCIÓN CIUDADA</strong></td></tr>'+
                                    '</th><th>Total</th><th>Ocupados</th><th>Vacantes</th><th>Avances %</th></tr></table></div>');
                tablaPromocion.find('#tablaPromocion').append( tablaResumenNodo.find('tr:last') );
                tablaPromocion.find('tr:last td:first').remove();

                tablaResumenNodo.find('tr:last').remove();
                tablaResumenNodo.find('tr:last').remove();

                $('#resumenNodo').html(tablaResumenNodo);
                $('#seccion_promocion').html( tablaPromocion );
            } else {
                $('#no_meta').html('0%');
                $('#resumenNodo').html('');
            }
        });

        $('#no_dependencias').html('<i class="fa fa-refresh fa-spin" style="font-size: x-large;"></i>');

        $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$('#municipio').val()+'&IdPuestoDepende='+node.key,
            function(result){
                $('#list_coordinados').html('');
                $('#list_vacantes').html('');

                if (result == null) {
                    result = {};
                }

                if (result.length>0) {
                    no_vacantes = 0;
                    no_coordinados = 0;
                    for(nodo in result) {
                        no_coordinados++;
                        nombre_coordinados = result[nodo].DescripcionPuesto;
                        $li = $('<div class="col-xs-3 col-sm-4 col-md-4" data-id="'+result[nodo].IdNodoEstructuraMov+'" data-persona="'+result[nodo].IdPersonaPuesto+'">'+
                                '<div class="thumbnail">'+
                                    '<img src="'+result[nodo].foto+'" class="img-rounded imgPerson">'+
                                    '<div class="caption">'+
                                        '<div class="text-center"><strong>'+result[nodo].DescripcionEstructura+'</strong></div>'+
                                        '<div class="text-center">'+result[nodo].NOMBRECOMPLETO+'</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>');

                        //$li.click(muestraDependiente);

                        $('#list_coordinados').append($li);

                        if (result[nodo].IdPersonaPuesto == '00000000-0000-0000-0000-000000000000') {
                            no_vacantes++;
                            $('#list_vacantes').append('<div class="col-xs-3 col-sm-4 col-md-4">'+
                                '<div class="thumbnail">'+
                                    '<img src="'+result[nodo].foto+'" class="img-rounded imgPerson">'+
                                    '<div class="caption">'+
                                        '<p class="text-center"><strong>'+result[nodo].DescripcionEstructura+'</strong></p>'+
                                        '<p class="text-center">Sin asignación</p><p>&nbsp;</p>'+
                                    '</div>'+
                                '</div>'+
                            '</div>');
                        }
                    }
                    nombre_coordinados = nombre_coordinados.replace('DE', '').replace('COORDINADOR', 'C.').toLowerCase() ;
                    $('#descripcion_dependencias').text(nombre_coordinados);
                    $('#no_dependencias').text(parseInt(no_coordinados));
                    $('#no_vacantes').text(parseInt(no_vacantes));
                } else {
                    $('#list_coordinados').append('<div class="alert alert-danger">Sin dependencias</div>');
                    $('#no_dependencias').text(0);
                    $('#no_vacantes').text(0);

                    $('#seccion_resumenNodo').hide();
                    $('#seccion_vacantes').hide();
                    $('#seccion_coordinados').hide();
                    $('#descripcion_dependencias').text('Sin Dependencias');
                }
            },
        "json");

        // Obtiene la estructura alterna
        $.ajax({
            url: urlTree,//urlTreeAltern
            dataType: "json",
            data: '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$('#municipio').val()+
                    '&IdPuestoDepende='+node.key+'&alterna=true',
            type: "POST",
            /*data: '_csrf='+$('[name=_csrf]').val()+'&idNodo='+node.key,
            type: "GET",*/
        }).done(function(response){
            if (response.length>0) {
                $('#infoEstrucAlterna span:first').text('1');
                treeEstrucAlterna.reload(response);
            } else {
                $('#infoEstrucAlterna span:first').text('0');
                $('#treeEstrucAlterna').hide();
            }
        });

        // Obtiene la meta seccional
        $.ajax({
            url: urlGetMetaBySeccion,
            dataType: "json",
            data: '_csrf='+$('[name=_csrf]').val()+'&id='+node.key+'&puesto='+node.data.IdPuesto,
            type: "GET",
        }).done(function(response){
            $('#no_meta_proyec').html(response);
        });

        // Obtiene la meta proyectada para los promotores
        $.ajax({
            url: urlGetAvanceMeta,
            //url: urlGetMetaByPromotor,
            dataType: "json",
            data: '_csrf='+$('[name=_csrf]').val()+'&id='+node.key,
            type: "GET",
        }).done(function(response){
            $('#no_meta_promocion').html(response+'%');
        });

        // Obtiene los programas
        $.ajax({
            url: urlGetProgramas,
            dataType: "json",
            data: '_csrf='+$('[name=_csrf]').val()+'&idMuni='+$('#municipio').val(),
            type: "GET",
        }).done(function(response){
            count = 0;

            if (response.length) {
                $tabla = '<table border="1" cellpadding="1" cellspacing="1" class="table table-condensed table-striped table-bordered table-hover">'+
                    '<thead><tr><th>Nombre</th><th>Integrantes</th><th>Sección</th></tr></thead><tbody>';

                for(fila in response) {
                    $tabla += '<tr><td>'+response[fila].Nombre+'</td><td class="text-center">'+response[fila].Integrantes+'</td><td class="text-center">'+
                            '<button class="btn btn-default" type="button" data-idorg="'+response[fila].IdOrganizacion+'">'+
                            '<span class="glyphicon glyphicon glyphicon-th-list" aria-hidden="true"></span></button></td></tr>';
                    count++;
                }
                $tabla += '</tbody></table>';


            } else {
                $tabla = 'No ha programas disponibles en este municipio';
                $('#seccion_programas').hide();
            }

            $('#list_programas').html($tabla);
            $('#list_programas button').click(getIntegrantesBySeccion);
            $('#no_programas').html(count);
            $('#list_integrantes').html('');
            $('#list_integrantes').hide();
        });
    };

    function getIntegrantesBySeccion() {
        idorg = $(this).data('idorg');

        $.ajax({
            url: urlGetIntegrantes,
            dataType: "json",
            data: {idOrg:idorg, idMuni:$('#municipio').val()},
            type: "GET",
        }).done(function(response) {
            if ( response.length ) {
                $tabla = 'Distribución de los integrantes por sección<table border="1" cellpadding="1" cellspacing="1" class="table table-condensed table-bordered table-hover">'+
                        '<thead><tr><th class="text-center">Sección</th><th class="text-center">Total</th><th class="text-center">Meta</th><th class="text-center">Integrantes</th></tr></thead><tbody>';

                for(fila in response) {
                    $tabla += '<tr class="text-center"><td>'+parseInt(response[fila].SECCION)+'</td><td>'+response[fila].total+'</td>'+
                            '<td>'+response[fila].MetaAlcanzar+'</td><td><button class="btn btn-default" type="button" data-idorg="'+idorg+'" '+
                            'data-seccion="'+parseInt(response[fila].SECCION)+'"><span class="glyphicon glyphicon glyphicon-th-list" aria-hidden="true"></span></button></tr>';
                }
                $tabla += '</tbody></table>';
            } else {
                $tabla = 'Sin integrantes'
            }
            $('#list_integrantes').html($tabla);
            $('#list_integrantes button').click(listIntegratesFromSeccion);
            $('#list_integrantes').toggle();
        });
    }

    function listIntegratesFromSeccion() {
        $('#modalListIntegrantes .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-pulse fa-lg"></i></div>');

        $.ajax({
            url: urlListInte,
            dataType: "json",
            data: {idOrganizacion:$(this).data('idorg'), idSeccion:$(this).data('seccion')},
            type: "GET",
        }).done(function(response) {
            if ( response.length ) {
                $tabla = '<table border="1" cellpadding="1" cellspacing="1" class="table table-condensed table-bordered table-hover">'+
                        '<thead><tr><th class="text-center">Nombre</th><th class="text-center">Sexo</th><th class="text-center">Fecha Nacimiento</th></tr></thead><tbody>';

                for(fila in response) {
                    $tabla += '<tr><td>'+response[fila].NOMBRE+'</td><td>'+response[fila].SEXO+'</td><td>'+response[fila].FECHANACIMIENTO+'</td></tr>';
                }
                $tabla += '</tbody></table>';
                $('#modalListIntegrantes .modal-body').html($tabla);
            }
        });

        $('#modalListIntegrantes').modal('show');
    }

    /*function muestraDependiente(event) {
        if($('#alertDescDependiente').length == 0) {
            $('#tabPuesto .panel-body').append('<div class="alert alert-success" role="alert" id="alertDescDependiente">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                '<div id="descDependiente" class="text-center"><i class="fa fa-refresh fa-spin" style="font-size: x-large;"></i></div>'+
            '</div>');
        } else {
            $('#descDependiente').html('<i class="fa fa-refresh fa-spin" style="font-size: x-large;"></i>');
        }
//        $self = this;
//
//        $('#modalPerson').modal('hide');
//        $('#modalPerson').on('hidden.bs.modal', function (e) {
//            verModalNodo(null, $($self).data('id'));
//            console.log('Oculto');
//        });

        if ($(this).data('persona') != '00000000-0000-0000-0000-000000000000') {
            $.ajax({
                url: urlPerson,
                dataType: "json",
                data: {id: $(this).data('persona')},
                type: "GET",
            }).done(function(response) {
                $descPersona = '<img src="'+response['foto']+'" class="img-rounded imgPerson"><br>'+
                    response.APELLIDO_PATERNO+' '+
                    response.APELLIDO_MATERNO+' '+
                    response.NOMBRE+'<br>'+
                    'Cel: '+(response.TELMOVIL == null ? '' : response.TELMOVIL)+'<br>'+
                    'e-mail: '+response.CORREOELECTRONICO+' ';
                $('#descDependiente').html($descPersona);
            });
        } else {
            $('#descDependiente').html('Puesto no Asignado');
        }
    }*/

    $('#dependencias').click(function(){
        $('#seccion_resumenNodo').hide();
        $('#seccion_vacantes').hide();
        $('#treeEstrucAlterna').hide();
        $('#seccion_programas').hide();
        $('#seccion_promocion').hide();
        $('#seccion_coordinados').toggle('slow', function() {
            if ($('#seccion_coordinados').is(':hidden')) {
                if ( $('#alertDescDependiente').length > 0 ) {
                    $('#alertDescDependiente').alert('close');
                }
            } else {
                $('#seccion_coordinados').ScrollTo();
            }
        });
    });

    $('#meta').click(function(){
        $('#seccion_coordinados').hide();
        $('#seccion_vacantes').hide();
        $('#treeEstrucAlterna').hide();
        $('#seccion_programas').hide();
        $('#seccion_promocion').hide();
        $('#seccion_resumenNodo').toggle('slow', function() {
            if (!$('#seccion_resumenNodo').is(':hidden')) {
                $('#seccion_resumenNodo').ScrollTo();
            }
        });

        if ( $('#alertDescDependiente').length > 0 ) {
            $('#alertDescDependiente').alert('close');
        }
    });

    $('#vacantes').click(function(){
        $('#seccion_coordinados').hide();
        $('#seccion_resumenNodo').hide();
        $('#treeEstrucAlterna').hide();
        $('#seccion_programas').hide();
        $('#seccion_promocions').hide();
        $('#seccion_vacantes').toggle('slow', function() {
            if (!$('#seccion_vacantes').is(':hidden')) {
                $('#seccion_vacantes').ScrollTo();
            }
        });

        if ( $('#alertDescDependiente').length > 0 ) {
            $('#alertDescDependiente').alert('close');
        }
    });

    $('#infoEstrucAlterna').click(function(){
        $('#seccion_coordinados').hide();
        $('#seccion_resumenNodo').hide();
        $('#seccion_vacantes').hide();
        $('#seccion_programas').hide();
        $('#seccion_promocion').hide();
        $('#treeEstrucAlterna').toggle('slow');
    });

    $('#btn_programas').click(function(){
        $('#seccion_coordinados').hide();
        $('#seccion_resumenNodo').hide();
        $('#seccion_vacantes').hide();
        $('#treeEstrucAlterna').hide();
        $('#seccion_promocion').hide();
        $('#seccion_programas').toggle('slow');
    });

    $('#meta_promocion').click(function(){
        $('#seccion_coordinados').hide();
        $('#seccion_resumenNodo').hide();
        $('#seccion_vacantes').hide();
        $('#treeEstrucAlterna').hide();
        $('#seccion_programas').hide();
        $('#seccion_promocion').toggle('slow');
    });

    $("#treeContainer").fancytree({
        extensions: ["table", "persist"],
        table: {
            indentation: 50
        },
        persist: {
            expandLazy: true,
            //overrideSource: true, // true: cookie takes precedence over `source` data attributes.
            store: "auto" // 'cookie', 'local': use localStore, 'session': sessionStore
        },
        source: $.ajax({
                url: urlTree,
                dataType: "json",
                data: $.cookie('parametros') ? $.cookie('parametros') : {'_csrf': $('input[name=_csrf]').val() },
                type: "POST",
            }).done(function(response){
                $('#loadIndicator').hide();
                if (response.length != 0) {
                    $('#alertResult').hide();
                    $("#treeContainer").removeAttr('style');

                    $('#treeContainer').ScrollTo();
                }
            }),
        checkbox: false,
        clickFolderMode: 3,
        lazyLoad: function(event, data) {
            var node = data.node;
            // Issue an ajax request to load child nodes
            data.result = {
                url: urlBranch,
                data: {idNodo: node.key}
            };
        },
        renderColumns: function(event, data) {
            var node = data.node,
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).addClass('text-center');
            if (node.data.persona == '00000000-0000-0000-0000-000000000000') {
                $tdList.eq(1).html('<a href="#" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-user"></span></a>');
            } else {
                $tdList.eq(1).html('<a href="#" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-user"></span></a>');
            }

            $tdList.eq(1).delegate("a", "click", verModalNodo);
        }
    });

    $("#treeEstrucAlterna").fancytree({
        extensions: ["table"],
        table: {
            indentation: 50
        },
        source: [],
        checkbox: false,
        clickFolderMode: 3,
        lazyLoad: function(event, data) {
            var node = data.node;
            // Issue an ajax request to load child nodes
            data.result = {
                url: urlBranch,
                data: {idNodo: node.key}
            };
        },
        renderColumns: function(event, data) {
            var node = data.node,
            $tdList = $(node.tr).find(">td");
            $tdList.eq(1).addClass('text-center');
            if (node.data.persona == '00000000-0000-0000-0000-000000000000') {
                $tdList.eq(1).html('<a href="#" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-user"></span></a>');
            } else {
                $tdList.eq(1).html('<a href="#" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-user"></span></a>');
            }
            $tdList.eq(1).delegate("a", "click", function(e){
                e.stopPropagation();  // prevent fancytree activate for this row
            });
        }
    });

    var tree = $("#treeContainer").fancytree("getTree");
    var treeEstrucAlterna = $("#treeEstrucAlterna").fancytree("getTree");

    $('#btnBuscar').click(function(){
        $parametros = '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$('#municipio').val()+
                    '&IdPuesto='+$('#puesto').val()+'&IdPuestoDepende=';

        var IdPuestoDepende = 0;

        $('[name=IdPuestoDepende]').each(function(index, element){
            IdPuestoDepende = $(this).val() != 0 ? $(this).val() : IdPuestoDepende;
        });

        $parametros += $parametros+IdPuestoDepende;

        // Se guardan los parámetros en una cookie para precargar la última búsqueda al entrar en esta sección
        $.cookie('parametros', $parametros, { path: '/' } );
        CookieParams = $.deparam($.cookie('parametros'));

        if ($('#municipio').val() == "") {
            $('#municipio').parent().addClass('has-error');
            $('#alertResult').html('Debe seleccionar un municipio');
            $('#alertResult').show();
            return false;
        } else {
            $('#alertResult').hide();
            $('#municipio').parent().removeClass('has-error');
        }

        $('#loadIndicator').show();

        tree.reload(
            $.ajax({
                url: urlTree,
                dataType: "json",
                data: $parametros,
                type: "POST",
            }).done(function(response){
                $('#loadIndicator').hide();
                if (response.length == 0) {
                    $('#alertResult').html('No se encontraron resultados en la b&uacute;squeda');
                    $('#alertResult').show();
                    $('#btnResumen').hide();
                    $("#treeContainer").attr({'style': 'display: none'});
                } else {
                    $('#alertResult').hide();
                    $('#btnResumen').show();
                    $("#treeContainer").removeAttr('style');

                    $('#treeContainer').ScrollTo();
                }
            })
        );

        $("#treeContainer").delegate("a", "click", verModalNodo);
    });

    $('#btnViewPerson').click(function(e){
        /*if ($(this).data('id') != '#') {
            $(this).attr('href', $(this).data('url')+'?id='+$(this).data('id'));
        } else {
            e.stopPropagation();
            e.preventDefault();
        }*/
        if ($(this).data('id') == '#') {
            alert('El puesto no ha sido asignado a una persona');
            e.stopPropagation();
            e.preventDefault();
        } else {
            $('#id').val($(this).data('id'));
        }
    });

    $('#btnResumen').click(function(){
        $('#loadIndicator').show();
        $.ajax({
            url: urlResumen,
            dataType: "json",
            data: {_csrf: CookieParams._csrf ,idMuni: $('#municipio').val()},
            type: "GET",
        }).done(function(response){
            $('#loadIndicator').hide();
            if (response.length == 0) {
                $('#alertResult').html('No se encontraron resultados en la b&uacute;squeda');
                $('#alertResult').show();
            } else {
                var fecha = new Date();
                $('#alertResult').hide();
                tablaResumen = ConvertJsonToTable(response, 'tablaResumen', 'table table-condensed table-striped table-bordered table-hover', 'Download');
                $('#modalResumen .table-responsive').html(tablaResumen);

                $('#tituloResumen').html(' Municipal de '+$('#municipio option:selected').text());
                $('#fechaResumen').html('Fecha de corte: '+padLeft(fecha.getDate(),2)+'-'+padLeft((fecha.getMonth()+1),2)+'-'+fecha.getFullYear());
                $('#modalResumen').modal('show');
            }
        });
    });

    function buildSelect(id, result) {
        filtro = '<div class="form-group filtroEstructura">'+
                '<label for="'+id+'">'+result[0].DescripcionPuesto+'</label>'+
                '<select id="'+id+'" class="form-control" name="IdPuestoDepende">'+
                '<option value="0">Todos</option>';

            for (var i=0; i<result.length; i++) {
                filtro += '<option value="'+result[i].IdNodoEstructuraMov+'" data-nivel="'+
                    result[i].Nivel+'">'+result[i].DescripcionEstructura+'</option>';
            }

            filtro += '</select></div>';

            $objFiltro = $(filtro);

            return $objFiltro.clone(true);
    }

    function agregaPuesto(result, id) {
        if (result.length>0) {
            $objFiltro = buildSelect(id, result);
            $objFiltro.find('select').change(agregaPuestoDepende);

            if($('#'+id).length) {
                $('#'+id).parent().replaceWith($objFiltro);
                $('#'+id).parent().nextAll().remove();
            } else {
                $('#bodyForm').append($objFiltro);
            }
        }
    }

    function agregaPuestoDepende() {
        if( $(this).val()!='' && $(this).val()!=0 ) {
            $('#loadIndicator').show();

            $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$('#municipio').val()+
                    '&Nivel='+$(this).find('option:selected').data('nivel')+'&IdPuestoDepende='+$(this).val(),
                    function(result){
                        if (result.length>0) {
                            id = doId(result[0].DescripcionPuesto);
                            agregaPuesto(result, id);
                        }
                    },
                "json").done(function(){ $('#loadIndicator').hide(); });
        } else {
            $(this).parent().nextAll().remove();
        }
    }

    $('#municipio').change(function(){
        $('#loadIndicator').show();
        $("#puesto option:first").text('Cargando datos...');
        $('.filtroEstructura').remove();

        var options = '<option value="0">Todos</option>';
        var idMuni = $(this).val();

        if (idMuni != '') {
            $('#btnResumen').show();
            $.getJSON(urlPuestos+'?_csrf='+$('[name=_csrf]').val()+'&idMuni='+idMuni, function(result) {
                for (var i=0; i<result.length; i++) {
                    options += '<option value="'+result[i].IdPuesto+'" data-nivel="'+result[i].Nivel+'">'+result[i].Descripcion+'</option>';
                }
                $("#puesto").html(options);
            }).done(function(result) {
                if (result.length>0) {
                    id = doId(result[0].Descripcion);
                    $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+idMuni,
                        function(result){ agregaPuesto(result, id); }, "json")
                        .done(function(){ $('#loadIndicator').hide(); });
                } else {
                    $('#loadIndicator').hide();
                }
            });
        } else {
            $('#loadIndicator').hide();
            $('#btnResumen').hide();
        }
    });

    $('#printResumen').click(function(){
        $imprimible = $('#modalResumen').clone();
        $imprimible.find('.modal-footer').remove();
        $imprimible.find('.close').remove();
        $imprimible.find('.panel').remove();
        $($imprimible).printArea({"mode":"popup","popClose":true});
    });

    $('#printResumenNodo').click(function(){
        $imprimible = $('<div class="box box-primary box-success"><div class="panel panel-success" id="containerPerson" style="margin-bottom: 1px !important;">'+
                '<div class="panel-body">'+
                '<h3 class="text-center" style="margin-top: 1px !important;"><strong>SIRECI</strong></h3>'+
                '<h4 class="text-center">ESTRATEGIA DE PROMOCIÓN CIUDADANA</h4>'+
                '<h5 class="text-center">STATUS DE LA ESTRUCTURA Y AVANCE MUNICIPAL DE '+$('#municipio option:selected').text()+'</h5>'+
                '</div></div></div>');
        $seccion_resumenNodo = $('#seccion_resumenNodo').clone().show();
        $seccion_resumenNodo.find('#tablaResumen').append( '<tr><td colspan="5"><strong>AVANCE DE LA META DE PROMOCIÓN CIUDADA</strong></td></tr>' );
        $seccion_resumenNodo.find('#tablaResumen').append( $('#tablaPromocion').find('tr:last').prepend('<td>PROMOCIÓN</td>') );
        $imprimible.find('.panel-body').append( '<div class="text-center col-xs-3">'+$('#imgPerson').parent().html()+'</div>');
        $imprimible.find('.panel-body').append( $('#frmPersonDetails').clone().addClass('col-xs-9') );
        $imprimible.find('.panel-body').append( $('#indicadoresPuesto').clone() );
        $imprimible.find('.panel-body').append( $('#seccion_coordinados').clone().show() );
        $imprimible.find('.panel-body').append( $seccion_resumenNodo );
        $imprimible.find('.panel-body').append( $('#fechaResumenNodo').clone() );
        $imprimible.find(' .btn.btn-app').blur();
        $imprimible.find('#verMasResumenNodo').remove();
        $imprimible.find('#btnAsignarPersona').remove();

        //console.log($imprimible.html());

        $($imprimible).printArea({"mode":"popup","popClose":true});
    });

});
