import './app';

let map;

function loadMap(center) {
    map = new google.maps.Map(document.getElementById('map'), {
        center,
        zoom: 12
    });

    fetch("/farm/all")
        .then(res => res.json())
        .then(farms => {
            farms.forEach(farm => {
                let marker = new google.maps.Marker({
                    position: {
                        lat: farm.address.position.latitude,
                        lng: farm.address.position.longitude
                    },
                    map,
                    title: farm.name,
                });
                marker.addListener("click", () => {
                    window.location.href = "/farm/" + farm.id + "/show";
                });
            });
        });
}

window.initMap = () => {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(position => {
            loadMap({
                lat: position.coords.latitude,
                lng: position.coords.longitude
            });
        });
    } else {
        loadMap({
            lat: 48.441049,
            lng: 1.546233
        });
    }
}

