$(document).ready(function(){
    $("#treeContainer").fancytree({
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
            $tdList.eq(1).html('<button type="button" class="btn btn-success btn-sm center-block">Ver</button>');
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
        
        if ($('#municipio').val() == "") {
            $('#municipio').parent().addClass('has-error');
            $('#alertResult').html('Debe seleccionar un municipio');
            $('#alertResult').show();
            return false;
        } else {
            $('#alertResult').hide();
            $('#municipio').parent().removeClass('has-error');
        }
        
        /*if ($('#puesto').val() == "") {
            $('#puesto').parent().addClass('has-error');
            $('#alertResult').html('Debe seleccionar un puesto');
            $('#alertResult').show();
            return false;
        } else {
            $('#alertResult').hide();
            $('#puesto').parent().removeClass('has-error');
        }*/
        
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
                    $("#treeContainer").attr({'style': 'display: none'});
                } else {
                    $('#alertResult').hide();
                    $("#treeContainer").removeAttr('style');

                    $('#treeContainer').ScrollTo();
                }
            })
        );
        
        $("#treeContainer").delegate("button", "click", function(e){
            $('#loadIndicator').show();
            var node = $.ui.fancytree.getNode(e);
            //var $button = $(e.target);
            e.stopPropagation();  // prevent fancytree activate for this row
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
                        {'colum': 'TELCASA', 'label': 'Tel. Casa'},
                        {'colum': 'TELMOVIL', 'label': 'Tel. Móvil'},
                        {'colum': 'SECCION', 'label': 'Sección'},
                        {'colum': 'SEXO', 'label': 'Género'},
                        {'colum': 'DISTRITO', 'label': 'Distrito'},
                    ];
                    
                    $tplFila = '<div class="form-group">'+
                        '<label class="col-sm-3 control-label">Puesto</label>'+
                        '<div class="col-sm-9">'+
                            '<div class="well well-sm">'+node.data.puesto+'</div>'+
                        '</div>'+
                    '</div>';
                    
                    $('#nombreCompleto').html($nombreCompleto);
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
        });
    });
    
    $('#btnViewPerson').click(function(){
        $(this).attr('href', $(this).data('url')+'?id='+$(this).data('id'));
    });
});