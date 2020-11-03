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
        <style> 
        </style>

    </head>
    <body>                
        <div class="container-fluid green-bg pt-3">            
            <div id="menue-content">   
                <div class="settings">
                    <a id="settings" class="" data-toggle="modal" data-target="#exampleModal" href="/settings">                    
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cog" class="svg-inline--fa fa-cog fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"></path></svg>                    
                    </a>
                    <a id="settings" class="" href="/statistics">                                        
                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="chart-bar" class=" svg-inline--fa fa-chart-bar fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M396.8 352h22.4c6.4 0 12.8-6.4 12.8-12.8V108.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v230.4c0 6.4 6.4 12.8 12.8 12.8zm-192 0h22.4c6.4 0 12.8-6.4 12.8-12.8V140.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v198.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h22.4c6.4 0 12.8-6.4 12.8-12.8V204.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v134.4c0 6.4 6.4 12.8 12.8 12.8zM496 400H48V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16zm-387.2-48h22.4c6.4 0 12.8-6.4 12.8-12.8v-70.4c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v70.4c0 6.4 6.4 12.8 12.8 12.8z"></path></svg>
                    </a>
                </div>
                            

            </div>            
            <div class="container">                
                <div class="text-center">
                    <h4><b>Veltins</b> <i style="text-decoration: underline;">Gastrosearch</i></h4>
                </div>                
                <div class="row">
                    <!-- was -->
                    <div class="col-12 mb-4">    
                        <label class="mr-sm-2" for="inlineFormCustomSelect">Kategorie</label>
                        <select class="custom-select mr-sm-2" id="type">
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
                          <option value="movie_theater">Kino</option>
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
                                    <option value="10000">+10 KM</option>
                                    <option value="20000">+20 KM</option>
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
            <div id="setting-container"></div>
            <div class="text-center">
                <p id="hits" class="badge badge-pill badge-dark hits"></p>
            </div>
            <div id="custom-error-alert" class="alert alert-danger alert-dismissible fade mt-4" >Es gibt noch unstimmigkeiten</div>
            <div class="d-nonex" id="download-sheet-input">
                <a class="btn btn-success d-none" href="" id="download-csv" >                    
                     Liste downloaden                     
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
        <script></script>
        <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
        
    </body>
</html>
