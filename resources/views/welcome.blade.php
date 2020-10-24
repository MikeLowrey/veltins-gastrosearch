<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <title>Laravel</title>
        <!-- Styles -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <style>
            .hide-this-element {display:none;}

            .pac-logo::after {
                background-image: none;                
            }
#pac-container {
  padding-bottom: 12px;
  margin-right: 12px;
  background-color: brown;
  width:100%;
}
.pac-target-input {
    width:100%;
}

.pac-controls {
  display: inline-block;
  padding: 5px 11px;
}

.pac-controls label {
  font-family: Roboto;
  font-size: 13px;
  font-weight: 300;
}

#pac-input {
  background-color: #fff;
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
  margin-left: 12px;
  padding: 0 11px 0 13px;
  text-overflow: ellipsis;
  width: 400px;
}

#pac-input:focus {
  border-color: #4d90fe;
}
        </style>
        <!-- Google Maps JavaScript library -->
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA"></script>
    </head>
    <body>
        <div>
            <select id="type">
                <option value="amusement_park">amusement_park</option>
                <option value="bar">bar</option>
                <option value="bowling_alley">bowling_alley</option>
                <option value="cafe">cafe</option>
                <option value="campground">campground</option>		  
                <option value="lodging">lodging</option>
                <option value="meal_delivery">meal_delivery</option>
                <option value="meal_takeaway">meal_takeaway</option>
                <option value="movie_theatre">movie_theatre</option>
                <option value="night_club">night_club</option>
                <option value="restaurant">restaurant</option>
            </select>
        </div>
        <div>
            <input type="range" min="1000" max="20000" value="2000" class="sliderx" id="range">
            <span id="range-value"></span> Meter
        </div>
        <input value="" placeholder="PLZ, Stadt eingeben" id="adress-input-field"/>
        <div>
            <button id="get-geodata-from-adress" onclick="init()">los!</button>        
        </div>
        <div>
            <button id="new-crawl-geodata-from-adress" class="hide-this-element" onclick="newCrawl()">Neu crawlen!</button>        
        </div>
        <div id="test"></div>
        <div id="output" style="background-color:#f1f1f1;">
            <ul id="myList"></ul>  
        </div>  

        <hr/>
        <form method="post" action="">
            <input type="text" name="location" id="location" value="<?php echo !empty($location)?$location:''; ?>" placeholder="Type location...">
            <input type="" name="loc_latitude" id="latitude" value="<?php echo !empty($latitude)?$latitude:''; ?>">
            <input type="" name="loc_longitude" id="longitude" value="<?php echo !empty($longitude)?$longitude:''; ?>">
            
            <select name="distance_km">
                <option value="">Distance</option>
                <option value="5" <?php echo (!empty($distance_km) && $distance_km == '5')?'selected':''; ?>>+5 KM</option>
                <option value="10" <?php echo (!empty($distance_km) && $distance_km == '10')?'selected':''; ?>>+10 KM</option>
                <option value="15" <?php echo (!empty($distance_km) && $distance_km == '15')?'selected':''; ?>>+15 KM</option>
                <option value="20" <?php echo (!empty($distance_km) && $distance_km == '20')?'selected':''; ?>>+20 KM</option>
            </select>
            <input type="submit" name="searchSubmit" value="Search" />
        </form>        
        <script>            
        var searchInput = 'location';
        autocomplete = new google.maps.places.Autocomplete((document.getElementById(searchInput)), {
            types: ['geocode'],
        });
        // Set initial restrict to the greater list of countries.
        autocomplete.setComponentRestrictions({
            country: ["de"],
        });
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var near_place = autocomplete.getPlace();
            document.getElementById('latitude').value = near_place.geometry.location.lat();
            document.getElementById('longitude').value = near_place.geometry.location.lng();
            console.log("->",document.getElementById('latitude').value)
        });

        document.getElementById('location').onchange = function () {            
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        };        
        </script>
        <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('js/all.js') }}"></script>
    </body>
</html>
