<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use \App\Models\PlacesItem;
use \App\Models\UserLocationRequest;
use \App\Models\RelSearchToPlace;
use \App\Http\Requests\RequestGeoDataForGooglePlacesCall;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use \App\Models\Setting;
use App\Repositories\PlacesItemRepository;

class GooglePlacesController extends Controller
{
    protected $places_item_repository;
    
    public function __construct() 
    {
        $this->places_item_repository = new PlacesItemRepository;
    }

    /**
     * only for test reasons
     *
     * @return void
     */
    public function test() {
    }

    /**
     * Get Places Items. First pass request parameter. then validate them. 
     * then check if the request with this place id and type and radius was 
     * allready searched. if yes the catch the request id and response 
     * all related places items. the relation table between places items and 
     * user localrequest is here the ref_search_to_places Table. If no call
     * the google places nearby api. catch the result and call next the google 
     * places detail api for phonenumber, website. merge both results and save
     * to our database. finaly response the places items. 
     *
     * @param RequestGeoDataForGooglePlacesCall $request
     * @return Array
     */
    public function call(RequestGeoDataForGooglePlacesCall $request): Array 
    {       
        return $this->places_item_repository->getPlacesItemsByParams($request);
    }  

    /**
     * Get geodata by location name. For Example: "Olsberg" or "59939"
     * Call the Google Maps geocode Api.
     *
     * @param Request $request
     * @return Array
     */
    public function get_geodata_from_google_geocode_api_by_name(Request $request): Array
    {
        // @todo implement request parameter validation inline
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', 
            [
                'address'=> $request->input("address"), 
                'key'=> $this->google_api_key,
                'components' => 'country:DE'
            ]
        );
        
        if ($response->status() == 200) {
                if($response->ok()) 
                {
                    $_json = $response->json();                    
                    if( $_json["results"][0]["geometry"]["location"] ) 
                    {                        
                        // prepare data for save request in database
                        $_data["location"] = $_json["results"][0]["geometry"]["location"];
                        $_data["place_id"] = $_json["results"][0]["place_id"];
                        $_data["formatted_address"] = $_json["results"][0]["formatted_address"];
                        $_data["request"] = trim(strtolower($request->input("address")));
                        $_data["type"] = ($request->input("type")) ? $request->input("type") : "bar";
                        $_data["radius"] = 1500;
                        // save data
                        $this->saveUserLocaltionRequest($_data);
                    }
                }                  
            return $_json["results"][0];            
        }
        return [];
    }    
}
