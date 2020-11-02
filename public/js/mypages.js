/**
 * statistics
 */
var map_is_loaded = false;
window.onscroll = function() {
    boundingClientRect = document.getElementById("map").getBoundingClientRect();
    if (!map_is_loaded && (boundingClientRect.top + (document.getElementById("map").offsetHeight / 2)) < window.innerHeight) {
        // console.log(boundingClientRect.top)    
        initMap();
    };
};

function initMap() {

    document.getElementById("filter-type").onchange = function() {
        var type = document.getElementById("filter-type").options[document.getElementById("filter-type").options.selectedIndex].value;
        refreshItems(type)
    }

    map_is_loaded = true;

    // set global vars
    var map;
    var markers = [];
    var placeCircle = []
    var items = [];
    var locations = [];
    var centerCoordinates = { lat: 51.3099808, lng: 8.5253748 };
    var zoomLevel = 9;

    // get  locations 
    locations = JSON.parse(document.getElementById('locations').getAttribute("data-locations"));
    items = locations;

    // init map
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: zoomLevel,
        center: centerCoordinates,
        mapTypeId: 'roadmap'
    });


    // setItemsOnMap           
    setItemsOnMap(items);

    function refreshItems(type) {
        let new_locations = locations.filter(function(item) {
            return item.type.includes(type)
        })
        removeItemsOnMap();
        setItemsOnMap(new_locations);
    }

    function removeItemsOnMap() {
        // Loop through markers and set map to null for each
        console.log({ markers: markers.length })
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        console.log({ placeCircleLength: placeCircle.length })
        for (var i = 0; i < placeCircle.length; i++) {
            placeCircle[i].setMap(null);
        }
        markers = [];
        placeCircle = [];
    }

    function bindMassageToMarker(_marker, data) {
        const infowindow = new google.maps.InfoWindow({
            content: data.radius.toString() + " km",
        });
        /*
        _marker.addListener("click", () => {
            infowindow.open(_marker.get("map"), _marker);
            map.panTo({lat: Number(JSON.parse(data.location).lat), lng: Number(JSON.parse(data.location).lng)})                                       
        });
        */
        _marker.addListener('mouseover', function() {
            infowindow.open(map, this);
            placeCircle
        });

        // assuming you also want to hide the infowindow when user mouses-out
        _marker.addListener('mouseout', function() {
            infowindow.close();
        });
    }

    function setMarkerOnMap(item) {
        var _marker = new google.maps.Marker({
            position: { lat: Number(JSON.parse(item.location).lat), lng: Number(JSON.parse(item.location).lng) },
            map: map,
            animation: google.maps.Animation.DROP,
            title: item.formatted_address,
            label: item.id.toString(),
            zIndex: item.id,
        });

        _marker.addListener("click", () => {
            map.setZoom(8);
            map.setCenter(_marker.getPosition(), google.maps.LatLng);
            // map.panTo({ lat: 8.5253748, lng: 51.3099808});
        });

        bindMassageToMarker(_marker, item)
        markers.push(_marker);
    }

    function setCircleOnMap(item) {
        const _placeCircle = new google.maps.Circle({
            strokeColor: "#F1f1f1",
            strokeOpacity: 0.2,
            strokeWeight: 1,
            fillColor: "#Ff1f1f1",
            fillOpacity: 0.09,
            map,
            center: { lat: Number(JSON.parse(item.location).lat), lng: Number(JSON.parse(item.location).lng) },
            radius: Math.sqrt(item.radius) * 100,
            clickable: true
        });

        google.maps.event.addListener(_placeCircle, 'mouseover', function(e) {
            document.getElementById("legende").innerHTML =
                "Kategorie: (" + item.type + ") <br/>" +
                "Ort: " + item.formatted_address + "<br/>" +
                "Radius: " + (item.radius / 1000) + " km <br/>" +
                "Treffer: " + item.items + "";

            _placeCircle.setOptions({
                strokeColor: "yellowgreen",
                strokeWeight: 3,
                strokeOpacity: 1,
            });
        });
        google.maps.event.addListener(_placeCircle, 'mouseout', function() {
            _placeCircle.setOptions({
                strokeColor: "#F1f1f1",
                strokeWeight: 1,
                strokeOpacity: 0.2,
            });
        })
        placeCircle.push(_placeCircle)

    }

    function setItemsOnMap(locations) {
        for (const i in locations) {
            setMarkerOnMap(locations[i])
            setCircleOnMap(locations[i])
        };
    }

}