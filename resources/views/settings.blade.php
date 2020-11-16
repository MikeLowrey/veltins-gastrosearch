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
        <script src="https://maps.googleapis.com/maps/api/js?v=3.11&sensor=false&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA" type="text/javascript"></script>
        <style>   
           
        </style>
    </head>
    <body> 
        <div class="container-fluid green-bg pt-3"> 
            <div id="content">  
                <div class="settings">
                    <a id="settings" href="/" alt="zur Suche">                    
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="search" class="svg-inline--fa fa-search fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg>
                    </a>                    
                </div> 

            </div>                            
            <div class="text-center">
                <h4><b>Veltins</b> <i style="text-decoration: underline;">Gastrosearch</i></h4>
                <b>SETTINGS</b>                                
            </div>                            
            <div class="container " style="min-height: 200px">
                <label class="mr-sm-2" for="inlineFormCustomSelect">Export Dateiformat</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Format</span>
                      </div>                
                    <select class="custom-select" id="select-file-format">                                          
                        <option {{$data['file_format'] == 'csv' ? 'selected' : ''}} value="csv" >CSV</option>
                        <option {{$data['file_format'] == 'xlsx' ? 'selected' : ''}} value="xlsx">Excel</option>                  
                    </select>                     
                    <div class="input-group-append">
                      <button class="btn btn-warning" id="file-format" type="button">speichern</button>                      
                    </div>                    
                </div>                  
                <br/>
                <label class="mr-sm-2" for="inlineFormCustomSelect">Wie lange sollen die Google Places Daten bei uns in der Datenbank liegen? Legen Sie hier die Cache Dauer in Tagen fest. </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Tage</span>
                      </div>                
                    <input class="form-control" id="input-cache-duration" value="{{$data['cache_duration']}}" type="number">       
                    <div class="input-group-append">
                      <button class="btn btn-warning" id="cache-duration" type="button">speichern</button>                      
                    </div>                    
                </div>                 

            </div>
        </div>
        <div class="container">
            <div class="separator mt-2">Versionen</div>
            <ul class="list-group">
                @foreach ($versions as $item)  
                    @if($item !== '')                   
                    <li class="list-group-item">{{$item}}</li>
                    @endif
                @endforeach
            </ul>            
        </div>
        <!-- toast -->
        <div id="snackbar"></div>

        <script></script>        
        <script type="text/javascript" src="{{ URL::asset('js/mypages.js') }}"></script>
</body>
</html>


