<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>      
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">        
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Veltins Gastrosearch</title>
    <!-- Styles -->            
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Google Maps JavaScript library -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA"></script>

    <!-- Custom styles for this template -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>

        </style>    
  </head>
  <body class="green-bg">
    <header>
        <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
            <h5 class="my-0 mr-md-auto "><a class="nav-brand-link" href="/"><strong>Veltins</strong> Gastrosearch</a></h5>
            <nav class="nava my-2 my-md-0 mr-md-3">
                <a id="settings" class="p-2 text-dark " href="/statistics">                                        
                    <svg aria-hidden="true" width="20" focusable="false" data-prefix="far" data-icon="chart-bar" class=" svg-inline--fa fa-chart-bar fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M396.8 352h22.4c6.4 0 12.8-6.4 12.8-12.8V108.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v230.4c0 6.4 6.4 12.8 12.8 12.8zm-192 0h22.4c6.4 0 12.8-6.4 12.8-12.8V140.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v198.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h22.4c6.4 0 12.8-6.4 12.8-12.8V204.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v134.4c0 6.4 6.4 12.8 12.8 12.8zM496 400H48V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16zm-387.2-48h22.4c6.4 0 12.8-6.4 12.8-12.8v-70.4c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v70.4c0 6.4 6.4 12.8 12.8 12.8z"></path></svg>
                </a>                   
                <a id="settings" class="p-2 text-dark " href="/settings">                    
                    <svg aria-hidden="true" width="20" focusable="false" data-prefix="fas" data-icon="cog" class="svg-inline--fa fa-cog fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"></path></svg>                            
                </a>
            </nav>            
        </div>         
    </header>

<main role="main">
    <div class="container">

    </div>
</main>
        <div class="container-fluid green-bgx pt-3">                           
            <div class="text-center">
                <h4><b>Gastrosearch</b> <i style="text-decoration: underline;">Einstellungen</i></h4>
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
            <div class="separator mt-2 mb-2"><h5>Versionen & Änderungen</h5></div>
            <table class="table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Änderungen</th>                    
                  </tr>
                </thead>
                <tbody>
                    @foreach ($versions as $item)  
                    @if($item !== '')                   
                    <tr>
                        <td>#{{ $loop->index }}</td>
                        <td><i>{{$item}}</i></td>                        
                    </tr>                    
                    @endif
                @endforeach                    
                </tbody>
            </table>         
        </div>
        <!-- toast -->
        <div id="snackbar"></div>

        <script></script>        
        <script type="text/javascript" src="{{ URL::asset('js/mypages.js') }}"></script>
</body>
</html>


