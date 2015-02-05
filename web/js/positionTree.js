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
                    $('#modalNoPerson').modal('show');
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
                        $('#imgPerson').attr('src', $('#imgPerson').data('path')+sexo+'.png');

                        $('#btnViewPerson').data('id', node.data.persona);

                        $('#loadIndicator').hide();
                        $('#modalPerson').modal('show');
                    });
                }
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

    $('#btnAddFilter').click(function(){
        $('#filtros').parent().removeClass('has-error');
        $('#alertFilter').hide();

        $selected = $('#filtros').val();

        if ($selected == '')  {
            $('#filtros').parent().addClass('has-error');
            $('#alertFilter').show();
        } else {
            $text = $('#filtros option:selected').text();
            $input = '<div class="form-group">'+
                        '<label for="'+$selected+'">'+$text+'</label> '+
                        '<select class="form-control" name="'+$selected+'">'+
                            '<option>Elija una opción</option>'+
                        '</select>'+
                    '</div>';
            $('#bodyForm').append($input);
            $('#filtros option:selected').remove();
            $('#modalAddFilter').modal('toggle');
        }
    });

    $('#btnBuscar').click(function(){
        $parametros = $('#formBuscar').serialize();
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
                $('#alertResult').hide();
                tablaResumen = ConvertJsonToTable(response, '', 'table table-condensed table-striped table-bordered table-hover', 'Download');
                $('#modalResumen .modal-body').html(tablaResumen);
                $('#modalResumen').modal('show');
            }
        })
    });
});