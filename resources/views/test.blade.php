<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>A Basic Map</title>
	<style>
		#map {
			height: 100%;
		}
		/* Optional: Makes the sample page fill the window. */
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}
	</style>
	<script>
	function initMap() {
		var mapOptions = {
			zoom: 6,
			center: new google.maps.LatLng(44, -110),
			mapTypeId: 'roadmap'
		};
        var map = new google.maps.Map(document.getElementById('map'), mapOptions);
        google.maps.event.addListener(map, 'click', function() {
            //alert(map.getBounds());
            // (42.09136772865683, -113.66119384765625), (45.84915232186155, -106.33880615234375)
            console.log(map.getBounds())
            const rectangle = new google.maps.Rectangle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.2,
                strokeWeight: 2,
                fillColor: "#FF0000",
                fillOpacity: 0.1,                
                bounds: {
                north: 42.09136772865683,
                south: 45.84915232186155,
                east: -106.33880615234375,
                west: -113.66119384765625,
                },
            });
            // setOnMap();
            rectangle.setMap(map);
        });   
         
        
	}
	</script>
</head>
<body>
<div id="map"></div>
<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA&callback=initMap">
</script>
</body>
</html>