<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PlacesItem;
use \App\Models\UserLocationRequest;
use Illuminate\Support\Facades\Http;

class GooglePlacesController extends Controller
{
    private $google_api_key = "";
    private $result_set = null;
    private $result_array_set = [];

    public function __construct() {
        $this->google_api_key = "AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA"; // @todo .env 
        $this->result_set = collect();
        /**
         * iterate nearbyPlaces mit dem token um alle zu erhalten
         * check ob item bereits in der datenbank. falls nein mache detail abfrage und speicher die 
         * frontend user logik
         *
         * @return void
         */
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function test(Request $request) 
    {
        // is address allread in database?        
        $_db = $this->search_places_data_in_database($request);
        
        if (count($_db) > 0) 
        {
            return $_db;
        }
        #dd("not in db");
        // get from google maps api the location coordinates
        $_gmap_geodata_api = null;
        $_gmap_geodata_api = $this->get_geodata_from_google_geocode_api_by_name($request);
        if (!$_gmap_geodata_api) {
            return ["not found"];
        }                
        // start to call google places api by lat lng type        
        $places_parameters = [    
            'type' => $request->input("type"),
            'lat' => $_gmap_geodata_api["geometry"]["location"]["lat"], // 51.3982179, // ms
            'lng' => $_gmap_geodata_api["geometry"]["location"]["lng"] // 8.5749432, // ms 
        ];        
        $_places_items = $this->get_places_data_from_google_api($places_parameters);
        
        // start to call google place details api
        if (!count($_places_items) > 0)
        {
            return ;
        }
        $_places_items_with_detials = $this->get_google_places_detials_by_place_id( $_places_items );
        #dd($_places_items_with_detials);
        // store results in databse
        array_walk($_places_items_with_detials, function($item) {
            $this->store_full_dataset( $item );
        });

        // response
        return $this->search_places_data_in_database($request);
    }

    /**
     * Finaly store the crawled dataset in the database
     *
     * @param Array $full_dataset
     * @return void
     */
    private function store_full_dataset(Array $full_dataset) 
    {
        // check if dataset allready exists
        if(true === PlacesItem::where([
            "place_id" => $full_dataset["place_id"]
            ])->exists()) {
                echo '<p>store full:' . $full_dataset["place_id"] . '</p>';
            return null;
        }
        $new_item = new PlacesItem();

        $new_item->place_id = $full_dataset["place_id"];
        $new_item->name = $full_dataset["name"];
        $new_item->types = implode(",",$full_dataset["types"]);
        $new_item->location = $full_dataset["geometry"]["location"];
        $new_item->place = $full_dataset["place"];
        $new_item->zip = $full_dataset["zip"];
        $new_item->street = $full_dataset["street"];
        $new_item->street_number = $full_dataset["street_number"];
        $new_item->country = $full_dataset["country"];
        $new_item->phone = $full_dataset["phone"];
        $new_item->website = $full_dataset["website"];
        $new_item->formatted_address = $full_dataset["formatted_address"];
        $new_item->user_ratings_total = isset($full_dataset["user_ratings_total"]) ? $full_dataset["user_ratings_total"] : 0;

        $new_item->save();
    }
    /**
     * Filter the passed array by passed name.
     *
     * @param Array $dataset
     * @param String $name
     * @return void
     */
    private function filter_value_from_google_address_components_by_name(Array $dataset, String $name) 
    {
        $r = NULL;
        $r = array_filter($dataset["result"]["address_components"], function ($var) use($name) {
            return ($var['types'][0] == $name);
        });
        if($r) {
            return array_values($r)[0]["long_name"];
        }         
        return null;              
    }
    /**
     * Call the google places api for recieving detailinformation about this place.
     * Passing two arrays. First is the resultset from the previews google places api call.
     * the secound parameter specified/descript what we are would like to receive.
     * After receiving data this function merge both two on.
     * 
     * @param Array $items
     * @param Array $fields
     * @return Array
     */
    public function get_google_places_detials_by_place_id(Array $items = [], Array $fields = ["name","rating","formatted_phone_number","website","address_component","adr_address","formatted_address"]) 
    {
        $results = []; 
        $fields = implode(",",$fields);
        foreach($items as $key => $item) 
        {
            // check if place_id allready exists
            if (true === PlacesItem::where([
                "place_id" => $item["place_id"]
                ])->exists() ) 
            {
                echo "<p>".$item["place_id"]."</p>";
                continue;
            }

            $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json',[
                    'place_id' => $item['place_id'], 
                    'fields' => $fields,
                    'key' => $this->google_api_key
                    ]);
            $results[$key] = $response->json();
            $items[$key]["phone"] = ( isset($response->json()['result']['formatted_phone_number']) ?  
                $response->json()['result']['formatted_phone_number'] : NULL );
            $items[$key]["website"] = ( isset($response->json()['result']['website']) ?  
                $response->json()['result']['website'] : NULL );
            $items[$key]["adr_address"] = ( isset($response->json()['result']['adr_address']) ?  
                $response->json()['result']['adr_address'] : NULL );
            $items[$key]["formatted_address"] = ( isset($response->json()['result']['formatted_address']) ?  
                $response->json()['result']['formatted_address'] : NULL );              
            $items[$key]["zip"] = $this->filter_value_from_google_address_components_by_name($response->json(), "postal_code");
            $items[$key]["street"] = $this->filter_value_from_google_address_components_by_name($response->json(), "route");
            $items[$key]["street_number"] = $this->filter_value_from_google_address_components_by_name($response->json(), "street_number");
            $items[$key]["street"] = $this->filter_value_from_google_address_components_by_name($response->json(), "route");
            $items[$key]["place"] = $this->filter_value_from_google_address_components_by_name($response->json(), "locality");                
            $items[$key]["country"] = $this->filter_value_from_google_address_components_by_name($response->json(), "country");                
        }        
        return $items;
    }

    public function get_geodata_by_name_from_db(Request $request) 
    {        
        return PlacesItem::all();
    }


    public function get_geodata_from_google_geocode_api_by_name(Request $request)
    {
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
                    #dd($_json);
                    if( $_json["results"][0]["geometry"]["location"] ) 
                    {                        
                        // prepare data for save request in database
                        $_data["location"] = $_json["results"][0]["geometry"]["location"];
                        $_data["place_id"] = $_json["results"][0]["place_id"];
                        $_data["formatted_address"] = $_json["results"][0]["formatted_address"];
                        $_data["request"] = trim(strtolower($request->input("address")));
                        // save data
                        $this->saveUserLocaltionRequest($_data);
                    }
                }                  
            return $_json["results"][0];            
        } 

        return null;
        
        //return $response->json();
        // place_id // ["geometry"]["location"]
        // dd($response->json());
        //"https://maps.googleapis.com/maps/api/geocode/json?address=Brilon,+CA&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA"
    }
    /**
     * Search for Dataset in Database by 
     *
     * @param Request $request
     * @return array
     */
    public function search_places_data_in_database(Request $request) 
    {          
        $_search_string = trim(strtolower($request->input("address")));
        return PlacesItem::where([    
                ['place', '=', $_search_string], //['place' => $_search_string],
                ['types', 'like', '%'.$request->input("type").'%']
                             
            ])->get();
    }

    public function search_by_place($request) 
    {          
        $_search_string = trim(strtolower($request->input("address")));

        $res = PlacesItem::where(
            [    
                ['place', '=', $_search_string],
                ['types', 'like', '%'.$request->input("type").'%']
                //['place' => $_search_string],                
            ]            
            )
            ->get();
            return $res;
    }
    /**
     * Call the google places nearbysearch api 
     *
     * @param Array $request
     * @return void
     */
    public function get_places_data_from_google_api(Array $request) {
        sleep(2);        
        $pagetoken = $type = "";
        $pagetoken = isset($request['pagetoken']) ? $request['pagetoken'] : null;
        if ( null !== $request['type'] ) {
            $type = $request['type'];
        } else {
            $type = "restaurant";
        }
        $response = [];   
        $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', 
            [
                'location'=> $request['lat'].",".$request['lng'],
                'radius' => 1500,
                'language' => 'de',
                'type' => $type,
                'key'=> $this->google_api_key,
                'pagetoken' => $pagetoken
            ]
        );  
        if ($response->status() == 200 && $response->successful() == true) 
        {
            $this->result_array_set = array_merge( $this->result_array_set, $response->json()["results"] );
            if ( isset ($response->json()["next_page_token"]) ) {            
                $request["pagetoken"] = $response->json()["next_page_token"];
                $this->get_places_data_from_google_api($request);
            }            
        } else {
            return $this->result_array_set;
        }        
        return $this->result_array_set;
    }

    /**
     * Store the recieved data from google maps geolocater api call.
     *
     * @param Array $data
     * @return void
     */
    private function saveUserLocaltionRequest(Array $data = NULL)
    {           
        $userLocaltionRequest = new UserLocationRequest();
        $userLocaltionRequest->request = $data["request"];
        $userLocaltionRequest->formatted_address = $data["formatted_address"];
        $userLocaltionRequest->place_id = $data["place_id"];
        $userLocaltionRequest->location = $data["location"];        
        $userLocaltionRequest->save();        
    }
}
