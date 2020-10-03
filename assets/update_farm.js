import './app';

class Address
{
    constructor(data, callback)
    {
        Object.assign(this, data);
        this.element = $(`<li>${this.formatted_address}</li>`);
        this.element.on("click", () => callback(this));
    }

    render()
    {
        $(".auto-complete").append(this.element);
    }

    get number()
    {
        return this.address_components.find(data => data.types.indexOf("street_number") >= 0).long_name
    }

    get route()
    {
        return this.address_components.find(data => data.types.indexOf("route") >= 0).long_name
    }

    get zipCode()
    {
        return this.address_components.find(data => data.types.indexOf("postal_code") >= 0).long_name
    }

    get city()
    {
        return this.address_components.find(data => data.types.indexOf("locality") >= 0).long_name
    }
}

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
        this.addresses = [];
        this.loadGeolocation();
        $("#farm_address_address").on("input", this.search.bind(this));
    }

    addMarker()
    {
        if (this.marker !== null) {
            this.marker.setMap(null);
        }
        if ($("#farm_address_position_latitude").val() !== "" && $("#farm_address_position_longitude").val() !== "") {
            this.map.setZoom(16);
            this.marker = new google.maps.Marker({
                position: {
                    lat: parseFloat($("#farm_address_position_latitude").val()),
                    lng: parseFloat($("#farm_address_position_longitude").val())
                },
                map: this.map,
                title: "Mon exploitation"
            });
        }
    }

    select(address)
    {
        $("#farm_address_address").val(address.number + " " + address.route);
        $("#farm_address_zipCode").val(address.zipCode);
        $("#farm_address_city").val(address.city);
        $("#farm_address_position_latitude").val(address.geometry.location.lat);
        $("#farm_address_position_longitude").val(address.geometry.location.lng);
        this.addresses = [];
        this.autoCompletion();
        this.addMarker();
    }

    search(e)
    {
        fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${e.currentTarget.value}&key=${process.env.GOOGLE_MAP_API_KEY}`, {
            method: 'get',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(json => {
                this.addresses = [];
                if (json.status === "OK") {
                    this.addresses = json.results.map(result => new Address(result, this.select.bind(this)));
                }
                this.autoCompletion();
            });
    }

    autoCompletion()
    {
        $(".auto-complete").empty();
        this.addresses.forEach(address => address.render());
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

        this.addMarker();
    }
}

window.initMap = () => {
    new Map();
}

