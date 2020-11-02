<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <title>Veltins Gastrosearch</title>
        <!-- Styles -->        
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <!-- Google Maps JavaScript library -->
        <!--script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA"></script--> 
        <script src="https://maps.googleapis.com/maps/api/js?v=3.11&sensor=false&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA" type="text/javascript"></script>
        <style>
            #map {
                height:600px;
                width:100%;
            }
        </style>
    </head>
    <body> 
        <div class="container-fluid green-bg pt-3"> 
            <div id="content">   
                <a id="settings" href="/">                    
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="search" class="settings svg-inline--fa fa-search fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg>
                </a>
            </div>                            
            <div class="text-center">
                <h4><b>Veltins</b> <i style="text-decoration: underline;">Gastrosearch</i></h4>
                <b>SETTINGS</b>                
                
            </div>                            
            <div class="container text-center">
                <div class="separator mt-2">Suchen insgesamt</div>
                <p> <b>{{count($data['user_requests'])}}</b></p>
                <div class="separator mt-2">Einträge insgesamt</div>
                <p> <b>{{$data['items_count']}}</b></p>
                <div class="separator mt-2">Relationen</div>
                <p><b>{{$data['items_relations']}}</b></p>
                <div class="separator mt-2">Geschätzte Kosten </div>
                <p><b>{{$data['items_relations'] * 0.026}} $</b></p>
            </div>
            <div style="display:none;" id="locations" data-locations="{{ json_encode($data['user_requests'])}}"></div>
        </div>
        <div class="container">
            <table class="table">
                <thead>
                <tr>
                    <th>Adresse</th>
                    <th>Kategorie</th>
                    <th>Radius</th>
                    <th>Treffer</th>
                </tr>
                </thead>
                @foreach ($data['user_requests'] as $item)
                    <tr>
                        <td>
                            ({{$item->id}}) 
                            {{$item->formatted_address}}<br/>                        
                            <small>{{$item->created_at}}</small>
                        </td>
                        <td>{{$item->type}}</td>
                        <td>{{$item->radius/1000}} km</td>
                        <td>{{$item->items}}</td>
                    </tr>
                @endforeach            
                <tbody>
                </tbody>
            </table> 
            <div class="separator mt-2">Kartenansicht</div>
            <div>
                <label class="mr-sm-2" for="inlineFormCustomSelect">Kategorie</label>
                <select class="custom-select mr-sm-2" id="filter-type">
                  <option value="0" selected>Bereich wählen...</option>
                  <option value="all">Alle</option>
                  <option value="amusement_park">Vergnügungspark</option>
                  <option value="bar">Bar</option>
                  <option value="bowling_alley">Bowlingbahn</option>
                  <option value="cafe">Café</option>
                  <option value="campground">Campingplatz</option>		  
                  <option value="lodging">Unterkünfte</option>
                  <option value="meal_delivery">Essenlieferung</option>
                  <option value="meal_takeaway">Essen zum Mitnehmen</option>
                  <option value="movie_theatre">Kino</option>
                  <option value="night_club">Nachtclub, Disko</option>
                  <option value="restaurant">Restaurant</option>
                </select>                
            </div>  
            <br/>   
            <div id="legende" style="display:none;" class="alert alert-warning"></div>
        </div>
        <div id="map"></div>
        
        <script>



        </script>        
        <script type="text/javascript" src="{{ URL::asset('js/mypages.js') }}"></script>
</body>
</html>


