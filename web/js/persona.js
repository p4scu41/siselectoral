var map;
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

    map = new google.maps.Map(document.getElementById("mapContainer"), mapOptions);

    var contentString = '<div id="mapInfo">' +
            '<p><strong>Casilla Electoral</strong><br>' +
            'Dirección<br>Referencias<br>' +
            'Responsable<br>' +
            'Otros datos</p>' +
            '</div>';

    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });

    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: "Casilla Electoral",
        maxWidth: 200,
        maxHeight: 200,
        icon: 'http://www.google.com/intl/en_us/mapfiles/ms/icons/red-dot.png'
    });

    google.maps.event.addListener(marker, 'click', function () {
        infowindow.open(map, marker);
    });
}

google.maps.event.addDomListener(window, 'load', initialize);

$('#modalMap').on('shown.bs.modal', function () {
    google.maps.event.trigger(map, "resize");
    map.setCenter(myLatlng);
});

/*$(document).ready(function(){
    $('.puesto').dotdotdot();
});*/