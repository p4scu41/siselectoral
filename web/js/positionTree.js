$(document).ready(function(){
    $('#btnAddFilter').click(function(){
        $('#filtros').parent().removeClass('has-error');
        $('#alertFilter').hide();
        
        $selected = $('#filtros').val();
        
        if ($selected == '')  {
            $('#filtros').parent().addClass('has-error');
            $('#alertFilter').show();
        } else {
            $('#modalAddFilter').modal('toggle');
            $text = $('#filtros option:selected').text();
            $input = '<div class="form-group">'+
                        '<label for="'+$selected+'">'+$text+'</label> '+
                        '<select class="form-control" name="'+$selected+'">'+
                            '<option>Elija una opción</option>'+
                        '</select>'+
                    '</div>';
            $('#bodyForm').append($input);
        }
    });
    
    $("#PositionTree").fancytree({clickFolderMode: 3});
});