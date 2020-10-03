import './app';

class Map
{
    constructor()
    {
        this.center = {
            lat: 48.441049,
            lng: 1.546233
        };
        this.map = null;
        this.marker = null;
        this.loadGeolocation();
    }

    loadGeolocation()
    {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(position => {
                this.center.lat = position.coords.latitude;
                this.center.lng = position.coords.longitude;
                this.loadMap();
            });
        } else {
            this.loadMap();
        }
    }

    loadMap() {
        this.map = new google.maps.Map(document.getElementById('map'), {
            center: this.center,
            zoom: 12
        });

        if ($("#farm_address_position_latitude").val() !== "") {
            this.marker = new google.maps.Marker({
                position: {
                    lat: parseFloat($("#farm_address_position_latitude").val()),
                    lng: parseFloat($("#farm_address_position_longitude").val())
                },
                map: this.map,
                title: "Mon exploitation",
            });
        }

        this.map.addListener("click", this.getPosition.bind(this));
    }

    getPosition(e) {
        this.center = {
            lat: e.latLng.lat(),
            lng: e.latLng.lng()
        };

        this.map.panTo(new google.maps.LatLng(this.center.lat, this.center.lng));

        this.marker = new google.maps.Marker({
            position: this.center,
            map: this.map,
            title: "Mon exploitation",
        });

        fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${this.marker.position.lat()},${this.marker.position.lng()}&key=${process.env.GOOGLE_MAP_API_KEY}`, {
            method: 'get',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(json => {
                let number = json.results[0].address_components.find(data => data.types.indexOf("street_number") >= 0).long_name;
                let route = json.results[0].address_components.find(data => data.types.indexOf("route") >= 0).long_name;
                let locality = json.results[0].address_components.find(data => data.types.indexOf("locality") >= 0).long_name;
                let postal_code = json.results[0].address_components.find(data => data.types.indexOf("postal_code") >= 0).long_name;

                $("#farm_address_address").val(number + " " + route);
                $("#farm_address_zipCode").val(postal_code);
                $("#farm_address_city").val(locality);
                $("#farm_address_position_latitude").val(this.marker.position.lat());
                $("#farm_address_position_longitude").val(this.marker.position.lng());
            })
    }
}

window.initMap = () => {
    new Map();
}

