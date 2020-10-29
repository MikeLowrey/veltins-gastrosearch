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
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA"></script>
    </head>
    <body>        
        <div class="container-fluid green-bg pt-3">            
            <div class="container">
                <div class="text-center">
                    <h4><b>Veltins</b> <i style="text-decoration: underline;">Gastrosearch</i></h4>
                </div>                
                <div class="row">
                    <!-- was -->
                    <div class="col-12 mb-4">    
                        <label class="mr-sm-2" for="inlineFormCustomSelect">Type</label>
                        <select class="custom-select mr-sm-2" id="type">
                          <option value="0" selected>Bereich wählen...</option>
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

                    <!-- wie -->
                    <div class="col-12">
                        <!-- address -->
                        <div class="row">                            
                            <div class="col-md-9">
                                <label class="mr-sm-2" for="inlineFormCustomSelect">Adresse / Stadt</label>
                                <input type="text" class="form-control" name="location" id="location" value="" placeholder="Type location..." class="pac-target-input" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="mr-sm-2" for="inlineFormCustomSelect">Umkreis</label>
                                <select class="custom-select mr-sm-2" id="radius" name="distance_km">
                                    <!--option value="">Umkreis</option-->
                                    <option value="1500" selected>+1,5 KM</option>
                                    <option value="5000">+5 KM</option>
                                    <option value="25000">+20 KM</option>
                                    <option value="50000">+50 KM</option>
                                </select>                                 
                            </div>
                            <input type="hidden" name="loc_latitude" id="latitude" value="">
                            <input type="hidden" name="loc_longitude" id="longitude" value="">
                            <input type="hidden" name="place_id" id="place-id" value="">
                            <input type="hidden" name="formatted-address" id="formatted-address" value="">
                        </div>
                        <div class="separator mt-2">ODER</div>
                        <!-- PLZ -->
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <label for="zip">PLZ</label>
                                <input type="text" class="form-control" id="zip" placeholder="PLZ" required autocomplete="off">
                            </div>                        
                        </div>
                    </div>

                    <!-- action -->
                    <div class="col-12 text-center mt-4 mb-3">
                        <button type="button" class="btn btn-warning" id="searchSubmit">Suche</button>
                        <div class="lds-ripple d-none"><div></div><div></div></div>                                                
                    </div>
                    
                        
                    
 
                </div>
            </div>


            <div class="container">
                <div class="row">                     
                    <div id="test"></div>    
                </div>
            </div>
        </div>


        <!-- output container -->
        <div class="container">
            <p id="hits" class="text-center hits"></p>
            <div id="custom-error-alert" class="alert alert-danger alert-dismissible fade mt-4" >Es gibt noch unstimmigkeiten</div>
            <div>
                <a class="btn btn-success" href="" id="download-csv" style="display:none;">                    
                     Liste downloaden
                     <svg width="12" aria-hidden="true" focusable="false" data-prefix="far" data-icon="file-excel" class="svg-inline--fa fa-file-excel fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm212-240h-28.8c-4.4 0-8.4 2.4-10.5 6.3-18 33.1-22.2 42.4-28.6 57.7-13.9-29.1-6.9-17.3-28.6-57.7-2.1-3.9-6.2-6.3-10.6-6.3H124c-9.3 0-15 10-10.4 18l46.3 78-46.3 78c-4.7 8 1.1 18 10.4 18h28.9c4.4 0 8.4-2.4 10.5-6.3 21.7-40 23-45 28.6-57.7 14.9 30.2 5.9 15.9 28.6 57.7 2.1 3.9 6.2 6.3 10.6 6.3H260c9.3 0 15-10 10.4-18L224 320c.7-1.1 30.3-50.5 46.3-78 4.7-8-1.1-18-10.3-18z"></path></svg>
                </a>
            </div>            
            <div id="output" style="background-color:#f1f1f1;">
                <ul id="myList"></ul>  
            </div>

            <table class="table" id="myTable">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Adresse</th>
                    <th>Tel</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>                                    
            </table>
        </div>

        <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
        <!--script type="text/javascript" src="{{ URL::asset('js/all.js') }}"></script-->
    </body>
</html>
