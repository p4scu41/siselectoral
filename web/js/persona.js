var mapDomicilio;
var mapCasilla;
myLatlng = new google.maps.LatLng(16.7528099, -93.1154969);

function initialize() {
    var mapOptions = {
        center: myLatlng,
        zoom: 15,
        mapTypeControl: false,
        panControl: false,
        rotateControl: false,
        streetViewControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    mapDomicilio = new google.maps.Map(document.getElementById("mapContainerDomicilio"), mapOptions);
    mapCasilla = new google.maps.Map(document.getElementById("mapContainerCasilla"), mapOptions);

    var contentString = '<div>' +
            '<p><strong>DATOS</strong><br>' +
            'Dirección<br>Referencias<br>' +
            'Responsable<br>' +
            'Otros datos</p>' +
            '</div>';

    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });

    var markerDomicilio = new google.maps.Marker({
        position: myLatlng,
        map: mapDomicilio,
        title: "Domicilio",
        maxWidth: 200,
        maxHeight: 200,
        icon: 'http://www.google.com/intl/en_us/mapfiles/ms/icons/red-dot.png'
    });

    var markerCasilla = new google.maps.Marker({
        position: myLatlng,
        map: mapCasilla,
        title: "Casilla Electoral",
        maxWidth: 200,
        maxHeight: 200,
        icon: 'http://www.google.com/intl/en_us/mapfiles/ms/icons/red-dot.png'
    });

    google.maps.event.addListener(markerDomicilio, 'click', function () {
        infowindow.open(mapDomicilio, markerDomicilio);
    });

    google.maps.event.addListener(markerCasilla, 'click', function () {
        infowindow.open(mapCasilla, markerCasilla);
    });
}

google.maps.event.addDomListener(window, 'load', initialize);

$('#modalMapDomicilio').on('shown.bs.modal', function () {
    google.maps.event.trigger(mapDomicilio, "resize");
    mapDomicilio.setCenter(myLatlng);
});

$('#modalMapCasilla').on('shown.bs.modal', function () {
    google.maps.event.trigger(mapCasilla, "resize");
    mapCasilla.setCenter(myLatlng);
});

/*$(document).ready(function(){
    $('.puesto').dotdotdot();
});*/