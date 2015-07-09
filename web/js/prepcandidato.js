$(document).ready(function(){
    $('#prepcandidato-id_tipo_eleccion').change(function(){
        $('.field-prepcandidato-municipio').addClass('hidden');
        $('.field-prepcandidato-distrito_local').addClass('hidden');
        $('.field-prepcandidato-distrito_federal').addClass('hidden');

        switch ($(this).val()) {
            case '1': // Presidencia Municipal
                $('.field-prepcandidato-municipio').removeClass('hidden');
                break;
            case '2': // Diputación Local
                $('.field-prepcandidato-distrito_local').removeClass('hidden');
                break;
            case '3': // Diputación Federal
                $('.field-prepcandidato-distrito_federal').removeClass('hidden');
                break;

        }
    });


});
