$(document).ready(function(){
    $('#padronglobal-alfa_clave_electoral').change(function(event){
        self = this;

        console.log($(this).val());

        $.ajax({
            url: getByClaveElectoral,
            dataType: "json",
            data: {clave: $(this).val()},
            type: "POST",
        }).done(function(response) {
            if(response.length) {
                $('#modalPersonaRegistrada .modal-body').html('<strong>La persona que esta registrando ya existe en la base de datos: <strong><p>'+
                    '<h3 class="text-center"><a href="'+viewPersona+'?id='+response[0].CLAVEUNICA+'">'+response[0].NOMBRE+' '+response[0].APELLIDO_PATERNO+' '+response[0].APELLIDO_MATERNO+'</a></h3><p>');
                $('#modalPersonaRegistrada').modal('show');
            }
        });
    });

});