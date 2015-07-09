$(document).ready(function(){
    $('#municipio').change(function(){
        $.ajax({
            url: getByMuni,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&muni=' + $('#municipio').val(),
            dataType: 'json',
        }).done(function(response){
            $('#prepcasillaseccion-id_seccion option').remove();
            $('#prepcasillaseccion-id_seccion').append('<option value="">Seleccione la sección</option>');

            for(item in response) {
                $('#prepcasillaseccion-id_seccion').append('<option value="'+response[item].id_seccion+'">'+response[item].seccion+'</option>');
            }
        });

    });


});
