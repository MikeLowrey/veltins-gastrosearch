<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <title>Laravel</title>
        <!-- Styles -->        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
body {
}
.green-bg {
    background-color: #096C30;
    color: white;
}
ul>li {
    color: black;
}
        </style>
        <!-- Google Maps JavaScript library -->
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA"></script>
    </head>
    <body>
        <div class="container-fluid green-bg">
            <div class="container">
                <div class="row">
    
    
                    <form method="get" action="">
                        <div>
    
                            <label class="mr-sm-2" for="inlineFormCustomSelect">Type</label>
                            <select class="custom-select mr-sm-2" id="type">
                              <option selected>Choose...</option>
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
                            <label class="mr-sm-2" for="inlineFormCustomSelect">Distance</label>
                            <select class="custom-select mr-sm-2" id="distance-km"  name="distance_km">
                                <option value="" selected>Distance</option>
                                <option value="5">+5 KM</option>
                                <option value="10">+10 KM</option>
                                <option value="15">+15 KM</option>
                                <option value="20">+20 KM</option>                            
                            </select>
                        </div>
    
                        <input type="text" name="location" id="location" value="<?php echo !empty($location)?$location:''; ?>" placeholder="Type location...">
                        <input type="" name="loc_latitude" id="latitude" value="<?php echo !empty($latitude)?$latitude:''; ?>">
                        <input type="" name="loc_longitude" id="longitude" value="<?php echo !empty($longitude)?$longitude:''; ?>">
                        <input type="" name="place_id" id="place-id" value="<?php echo !empty($longitude)?$longitude:''; ?>">
                        <input type="" name="formatted-address" id="formatted-address" value="<?php echo !empty($longitude)?$longitude:''; ?>">
                        
                        <select name="distance_km">
                            <option value="">Distance</option>
                            <option value="5" <?php echo (!empty($distance_km) && $distance_km == '5')?'selected':''; ?>>+5 KM</option>
                            <option value="10" <?php echo (!empty($distance_km) && $distance_km == '10')?'selected':''; ?>>+10 KM</option>
                            <option value="15" <?php echo (!empty($distance_km) && $distance_km == '15')?'selected':''; ?>>+15 KM</option>
                            <option value="20" <?php echo (!empty($distance_km) && $distance_km == '20')?'selected':''; ?>>+20 KM</option>
                        </select>
                        <input type="submit" id="searchSubmit" value="Search" />
                    </form>        
     
                    
    
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                          <label for="validationTooltip03">City</label>
                          <input type="text" class="form-control" id="validationTooltip03" placeholder="City" required>
                          <div class="invalid-tooltip">
                            Please provide a valid city.
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <label for="validationTooltip04">State</label>
                          <input type="text" class="form-control" id="validationTooltip04" placeholder="State" required>
                          <div class="invalid-tooltip">
                            Please provide a valid state.
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <label for="validationTooltip05">Zip</label>
                          <input type="text" class="form-control" id="validationTooltip05" placeholder="Zip" required>
                          <div class="invalid-tooltip">
                            Please provide a valid zip.
                          </div>
                        </div>
                    </div>                
    
                    <hr/>
                    <div id="test"></div>    
                </div>
            </div>
        </div>
        <div class="container">
            <br/>
            <div>
                <a  class="btn btn-success" href="/download/12" id="download-csv">download</a>
            </div>            
            <div id="output" style="background-color:#f1f1f1;">
                <ul id="myList"></ul>  
            </div>  
        </div>



                

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
            window.t = near_place;
            console.log("near_place",near_place);
            document.getElementById('latitude').value = near_place.geometry.location.lat();
            document.getElementById('longitude').value = near_place.geometry.location.lng();
            document.getElementById('place-id').value = near_place.place_id;
            document.getElementById('formatted-address').value = near_place.formatted_address;            
        });

        document.getElementById('location').onchange = function () {            
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
            document.getElementById('place-id').value = '';
        };        
        document.getElementById("searchSubmit").onclick = function() {
            // clear outpulist and list element
            document.getElementById("myList").innerHTML = ''

            let lat = document.getElementById('latitude').value;
            let lng = document.getElementById('longitude').value;
            let placeid = document.getElementById('place-id').value;
            let type = document.getElementById("type").options[document.getElementById("type").options.selectedIndex].value;
            let radius = "1500";
            let formatted_address = document.getElementById('formatted-address').value
            // type , radius
            let url = "api/testnew" + "?lat=" + lat + "&lng=" + lng + "&type=" + type + "&radius="+radius+"&placeid="+placeid + "&formattedaddress="+formatted_address;

            console.log(callApi(url));
            return false;
            fetch(url)
                .then(function(data) {
                    
                    _json2 = data.json();
                    console.log("data",_json2);
                    document.getElementById("output").innerHTML = _json2;                    
                    })                
                    .catch(function(error) {
                        // If there is any error you will catch them here
                        console.error("hilfre")
                    });     

                    /*
            try {
                let response =  fetch(url);
                console.log("url step 2", url);

            } catch (err) {
                // catches errors both in fetch and response.json
                alert("step2");
                console.log("err step 2", err);
            }   
            */
            return false;         
        }
        async function callApi(url) {
            try {
                let response = await fetch(url);
                console.log("url step 2", url);
                _json2 = await response.json();
                console.log("response", _json2);
                // document.getElementById("output").innerHTML = _json2;
                BuildList(_json2)
            } catch (err) {
                // catches errors both in fetch and response.json
                alert("step2");
                console.log("err step 2", err);
            }                        
        }

        function BuildList(obj) {
            Object.entries(obj.results).forEach(([key, value]) => {
                console.log(`${key}: ${value.name}`)                
                let node = document.createElement("LI");
                let textnode = document.createTextNode(value.name + "-" + value.place_id + "-" + value.formatted_address + "-" + value.types);
                // let textnode = document.createTextNode(value.join());
                node.appendChild(textnode);
                document.getElementById("myList").appendChild(node);                 
            });            
           
        }
        </script>
        <!--script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script-->
        <!--script type="text/javascript" src="{{ URL::asset('js/all.js') }}"></script-->
    </body>
</html>
