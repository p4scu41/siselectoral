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

    $('#persona_promueve').change(function(event) {
        cargaOtrasPromociones($(this).val());
    });

    $('#persona_promueve').change(function(event) {
        cargaOtrasPromociones($(this).val());
    });

    $('#puestos_promovidos').change(function(event) {
        console.log($(this).val());
        console.log($(this).find('option:selected').text());

        $('#persona_puesto').val($(this).val());
        $('#persona_puesto').trigger('change');
    });
});

function cargaOtrasPromociones(id)
{
    $.post(urlOtrosPromocion, '_csrf='+yii.getCsrfToken()+'&id='+id,
        function(result) {
            $('#puestos_promovidos option').remove();
            $('#puestos_promovidos').append('<option value=""></option>');

            if (result.length>0) {
                for (var i = 0; i < result.length; i++) {
                    $('#puestos_promovidos').append('<option value="'+result[i].id+'">' + 
                        (result[i].Descripcion ? result[i].Descripcion : "") + " " + result[i].NombreCompleto +
                        '</option>');
                }
            }
        },
    "json");
}