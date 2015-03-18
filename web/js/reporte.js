$(document).ready(function(){
    $form = $('<form action="" method="post" target="_blank">'+
            '<input type="text" name="title" id="title">'+
            '<textarea name="content" id="content"></textarea>'+
            '<input type="hidden" name="_csrf" value="'+$('[name=_csrf]').val()+'"></form>');

    $('#btnExportPdf, #btnExportExcel').click(function(event){
        content = $('#reporteContainer').html();

        if ($(this).attr('id') == 'btnExportExcel') {
            content = $('#reporteContainer table').table2CSV({delivery: 'value'});
        }

        $form.find('#content').text( content );
        $form.find('#title').val( $('#titulo').html() );
        $form.attr('action', $(this).data('url'));
        $form.submit();

        event.stopPropagation();
        event.preventDefault();
        return false;
    });

});