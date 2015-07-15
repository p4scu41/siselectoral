$(document).ready(function(){
    $('#municipio').change(function(){
        $.ajax({
            url: getByMuni,
            type: 'POST',
            data: '_csrf='+$('[name=_csrf]').val()+'&muni=' + $('#municipio').val(),
            dataType: 'json',
        }).done(function(response){
            if ($('#prepcasillaseccion-id_seccion').length) {
                $('#prepcasillaseccion-id_seccion option').remove();
                $('#prepcasillaseccion-id_seccion').append('<option value="">Seleccione la sección</option>');

                for(item in response) {
                    $('#prepcasillaseccion-id_seccion').append('<option value="'+response[item].id_seccion+'">'+response[item].seccion+'</option>');
                }
            } else {
                $('#prepcasillaseccionsearch-id_seccion option').remove();
                $('#prepcasillaseccionsearch-id_seccion').append('<option value="">Seleccione la sección</option>');

                for(item in response) {
                    $('#prepcasillaseccionsearch-id_seccion').append('<option value="'+response[item].id_seccion+'">'+response[item].seccion+'</option>');
                }
            }
        });

    });

    $('.btnAcivarCasilla').click(function(event){
        event.stopPropagation();
        event.preventDefault();
        self = this;
        
        $.ajax({
            url: urlChangeactivo,
            type: 'post',
            dataType: 'json',
            data: 'id='+$(self).data('id')
        }).done(function(respuesta){
            if ($(self).find('i').hasClass('fa-check')) {
                $(self).find('i').removeClass('fa-check');
                $(self).find('i').addClass('fa-times');
            } else if ($(self).find('i').hasClass('fa-times')) {
                $(self).find('i').removeClass('fa-times');
                $(self).find('i').addClass('fa-check');
            }
        });
    });


});
