jQuery(document).ready(function($){
    $('.btnVerOrganizacion').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        
        if ($(this).text() == 0) {
            $('#modalOrganizaciones .modal-body').html('Sin Organizaciones');
        } else {
            $.post(urlGetOrganizaciones, 'id='+$(this).data('id'), function(respuesta){
                $('#modalOrganizaciones .modal-body').html(ConvertJsonToTable(respuesta, '', 'table table-condensed table-striped table-bordered table-hover', 'Download'));
            });
        }
        
        $('#modalOrganizaciones').modal('show');
    });


    $('#btnExportPdf').click(function(event) {
        $form = $('#frmSearchPromocion').clone();

        $form.attr('action', $(this).data('url'));
        $form.attr('target', '_blank');
        $('#formExport').html($form);
        $form.submit();

        event.stopPropagation();
        event.preventDefault();
        return false;
    });
});