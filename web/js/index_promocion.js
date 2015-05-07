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
});