$(document).ready(function(){
    $form = $('<form action="" method="post" target="_blank" style="display:none">'+
            '<input type="text" name="title" id="title">'+
            '<textarea name="content" id="content"></textarea>'+
            '<input type="hidden" name="_csrf" value="'+$('[name=_csrf]').val()+'"></form>');

    $('.btnExportPdf, .btnExportExcel').click(function(event){
        content = $('#reporteContainer').html();

        if ($(this).hasClass('btnExportExcel')) {
            content = $('#reporteContainer table').table2CSV({delivery: 'value'});
        }

        $form.find('#content').text( content );
        $form.find('#title').val( $('#titulo').html() );
        $form.attr('action', $(this).data('url'));
        $('#formExport').html($form);
        $form.submit();

        event.stopPropagation();
        event.preventDefault();
        return false;
    });

    $('#tipoEleccion').change(function(){
        $('#municipio').closest('div').addClass('hidden');
        $('#distritoLocal').closest('div').addClass('hidden');
        $('#distritoFederal').closest('div').addClass('hidden');
        $('#div_zonas').closest('div').addClass('hidden');

        $('#municipio option:first').prop('selected', true);
        $('#distritoLocal option:first').prop('selected', true);
        $('#distritoFederal option:first').prop('selected', true);
        $('#zona option:not(:first)').remove();
        $('#alertResult').html('');

        switch ($(this).val()) {
            case '1': // Presidencia Municipal
                $('#municipio').closest('div').removeClass('hidden');
                break;
            case '2': // Diputación Local
                $('#distritoLocal').closest('div').removeClass('hidden');
                break;
            case '3': // Diputación Federal
                $('#distritoFederal').closest('div').removeClass('hidden');
                break;
        }
    });

    $('#btnGenerarReporte, #btnReporteSeccional').click(function(event) {
        $('.alert').remove();

        switch ($('#tipoEleccion').val()) {
            case '1': // Presidencia Municipal
                    if ($('#municipio').val() == '' || typeof($('#municipio').val()) == 'undefined') {
                        $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Municipio</div>');
                        return false;
                    }
                break;
            case '2': // Diputación Local
                    if ($('#distritoLocal').val() == '' || typeof($('#distritoLocal').val()) == 'undefined') {
                        $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Distrito Local</div>');
                        return false;
                    }
                break;
            case '3': // Diputación Federal
                    if ($('#distritoFederal').val() == '' || typeof($('#distritoFederal').val()) == 'undefined') {
                        $('#alertResult').html('<div class="alert alert-danger">Debe seleccionar el Distrito Federal</div>');
                        return false;
                    }
                break;
        }

        $('#loadIndicator').show();
        $('#div_loading').show();

        $.ajax({
            type: 'POST',
            url: urlReporte,
            data: $('#formBuscar').serialize()+'&tipoReporte=1',
            dataType: 'json',
            success: function(result) {
                $('#titulo').html(result.titulo);
                $('#tabla_reporte').html(result.reporteHTML);

                $('#tabla_reporte tr td:nth-child(3)').css('cursor', 'pointer');
                $('#tabla_reporte tr td:nth-child(3)').on('click', function(event){
                    console.log($(this).text());
                });

                $('#loadIndicator').hide();
                $('.opcionesExportar').show();
                $('#div_loading').fadeOut('slow');
            }
        });
    });

    $('#municipio, #distritoLocal, #distritoFederal').change(function(){
        //loadCandidatos.apply(this);
        loadZonas.apply(this);
    });

});

function loadZonas()
{
    $('#loadIndicator').show();

    $.ajax({
        url: urlGetZonas,
        method: 'POST',
        dataType: 'json',
        data: $('#formBuscar').serialize()
    }).done(function(response) {
        $('#loadIndicator').hide();
        $('#div_zonas').removeClass('hidden');

        $('#zona option:not(:first)').remove();

        if (response.length) {
            for (zona in response) {
                $('#zona').append('<option value="'+response[zona].zona+'">'+response[zona].zona+'</option>');
            }
        }
    });
}