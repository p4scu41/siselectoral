$(document).ready(function(){
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
        
        $("#treeContainer").fancytree({
            clickFolderMode: 3,
            source:[{"key": "34260", "title": "CS TUXTLA GUTIERREZ 1", "folder": true, "lazy": true},{"key": "34261", "title": "CS TUXTLA GUTIERREZ 2", "folder": true, "lazy": true}],
            /*source: {
                url: urlTree+'?'+$parametros,
                cache: false
            },*/
            lazyLoad: function(event, data) {
                var node = data.node;
                // Issue an ajax request to load child nodes
                data.result = {
                    url: urlBranch,
                    data: {idNodo: node.key}
                }
            }
        });
    });
});