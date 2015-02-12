$(document).ready(function(){
    var CookieParams = null;

    if ($.cookie('parametros')) {
        CookieParams = $.deparam($.cookie('parametros'));

        $('#btnResumen').show();
        $('#municipio option[value='+parseInt(CookieParams.Municipio)+']').attr('selected', true);
        $('#puesto option[value='+parseInt(CookieParams.IdPuesto)+']').attr('selected', true);
    }

    var verModalNodo = function(e){
        $('#loadIndicator').show();
        var node = $.ui.fancytree.getNode(e);
        //var $button = $(e.target);
        e.stopPropagation();  // prevent fancytree activate for this row
        e.preventDefault();
        if (node.data.persona == '00000000-0000-0000-0000-000000000000') {
            $('#loadIndicator').hide();
            $('#titulo_puesto').html(node.title.replace(' - ', '<br>').replace(/\[\d+\]/,''));
            $('#frmPersonDetails').html('<div class="alert alert-danger"><i class="fa fa-frown-o fa-lg"></i> Puesto no asignado</div>');
            $('#modalPerson').modal('show');
        } else {
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
                    {'colum': 'DISTRITO', 'label': 'Distrito'},
                ];

                $tplFila = '<div class="form-group">'+
                    '<label class="col-sm-3 control-label">Persona</label>'+
                    '<div class="col-sm-9">'+
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
                        '<label class="col-sm-3 control-label">'+$datos[$fila].label+'</label>'+
                        '<div class="col-sm-9">'+
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
                $('#resumenNodo').html('<i class="fa fa-refresh fa-spin" style="font-size: x-large;"></i>');
            });
        }

        $.ajax({
            url: urlResumenNodo,
            dataType: "json",
            data: {idNodo: node.key},
            type: "GET",
        }).done(function(response) {
            tablaResumenNodo = ConvertJsonToTable(response, 'tablaResumen', 'table table-condensed table-striped table-bordered table-hover', 'Download');
            $('#resumenNodo').html(tablaResumenNodo);
        });

        $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$('#municipio').val()+'&IdPuestoDepende='+node.key,
            function(result){
                $('#list_coordinados').html('');
                $('#list_vacantes').html('');

                if (result.length>0) {
                    no_vacantes = 0;
                    no_coordinados = 0;
                    for(nodo in result) {
                        no_coordinados++;
                        nombre_coordinados = result[nodo].DescripcionPuesto;
                        $('#list_coordinados').append('<li>'+result[nodo].DescripcionPuesto+' - '+result[nodo].DescripcionEstructura+'</li>');

                        if (result[nodo].IdPersonaPuesto == '00000000-0000-0000-0000-000000000000') {
                            no_vacantes++;
                            $('#list_vacantes').append('<li>'+result[nodo].DescripcionPuesto+' - '+result[nodo].DescripcionEstructura+'</li>');
                        }
                    }
                    //$('#nombre_coordinados').text(nombre_coordinados);
                    $('#no_coordinados').text(parseInt(no_coordinados));
                    $('#no_vacantes').text(parseInt(no_vacantes));
                } else {
                    $('#list_coordinados').append('<div class="alert alert-danger">Sin dependencias</div>');
                    $('#no_coordinados').text(0);
                    $('#no_vacantes').text(0);
                    //$('#nombre_coordinados').text('');
                }
            },
        "json");
    };

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

    var tree = $("#treeContainer").fancytree("getTree");

    $('#btnBuscar').click(function(){
        $parametros = '_csrf='+$('[name=_csrf]').val()+'&Municipio='+$('#municipio').val()+
                    '&IdPuesto='+$('#puesto').val()+'&IdPuestoDepende=';

        var IdPuestoDepende = 0;

        $('[name=IdPuestoDepende]').each(function(index, element){
            IdPuestoDepende = $(this).val() != 0 ? $(this).val() : IdPuestoDepende;
        });

        $parametros += $parametros+IdPuestoDepende;

        // Se guardan los parámetros en una cookie para precargar la última búsqueda al entrar en esta sección
        $.cookie('parametros', $parametros);
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

    $('#btnViewPerson').click(function(){
        $(this).attr('href', $(this).data('url')+'?id='+$(this).data('id'));
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
    });

    $('#printResumen').click(function(){
        $imprimible = $('#modalResumen').clone();
        $imprimible.find('.modal-footer').remove();
        $imprimible.find('.close').remove();
        $imprimible.find('.panel').remove();
        $($imprimible).printArea({"mode":"popup","popClose":true});
    });

});