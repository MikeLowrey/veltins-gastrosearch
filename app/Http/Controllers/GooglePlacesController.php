<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PlacesItem;
use \App\Models\UserLocationRequest;
use \App\Models\RelSearchToPlace;
use \App\Http\Requests\RequestGeoDataForGooglePlacesCall;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GooglePlacesController extends Controller
{
    /**
     * Google Key. It appending on the request url as parameter.
     *
     * @var string
     */
    private $google_api_key = "";
 
    /**
     * Google Places Types of our interesst
     *
     * @var array
     */
    private $google_places_api_types = [
        "amusement_park",
        "bar",
        "bowling_alley",
        "cafe",
        "campground",
        "lodging",
        "meal_delivery",
        "meal_takeaway",
        "movie_theatre",
        "night_club",
        "restaurant"
    ];

    /**
     * Between every google places nearby api call it is useful 
     * to wait any secound before start the next request. Avoiding
     * some problems with google restrictions.
     * 
     * @var integer
     */
    private $sleeping_in_seconds = 1;

    private $result_array_set = [];
    
    public function __construct() 
    {
        $this->google_api_key = env('GOOGLE_MAPS_API_KEY', NULL);        
    }

    public function test() {
        $data = [
            'name' => '',
            'items_relations' => DB::table('rel_search_to_places')->count(),
            'items_count' => PlacesItem::count(),
            'user_requests' => DB::table('user_location_requests as r')                
            ->select('r.*',DB::raw('count(rel.id) as items'))
            ->join('rel_search_to_places as rel', 'rel.user_request_id', 'r.id')
            ->groupBy('r.id')
            ->get()->all()
        ];
        return view('settings')->with('data',$data);
        return DB::table('rel_search_to_places')->count();
        return PlacesItem::count();
        // get user request and their counting items
        return DB::table('user_location_requests as r')                
        ->select('r.*',DB::raw('count(rel.id) as items'))
        ->join('rel_search_to_places as rel', 'rel.user_request_id', 'r.id')
        ->groupBy('r.id')
        ->get();


        // get USer requests
        return UserLocationRequest::all();
        // done
        $request['type'] = 'bar';

        $i = DB::table('rel_search_to_places')
        ->join('places_items', 'places_items.place_id', '=', 'rel_search_to_places.places_id')
        ->where([
            ["rel_search_to_places.user_request_id","=", 6 ],
            ["places_items.types", "LIKE", "%".$request['type']."%" ]
        ])
        ->groupBy('places_items.place_id')
        ->select('places_items.*')
        ->get();
        return [
            "count"=> count($i), "i"=>$i
        ];
       
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
    public function test_new(RequestGeoDataForGooglePlacesCall $request): Array 
    {        
        $request->validated();

        $userLocations = UserLocationRequest::where([
            ["place_id","=",$request->input("placeid")],
            ["type", "=", $request->input("type")],
            ["radius", "=", $request->input("radius")]
        ])->first();
        
        // Check if there was a similar request (radius, localtion) in the past but with type = all.
        $isSimilarUserLocationRequestWithTypeAll = UserLocationRequest::where([
            ["place_id","=",$request->input("placeid")],
            ["type", "=", 'all'],
            ["radius", "=", $request->input("radius")]
        ])->first();
        if (!$userLocations && $isSimilarUserLocationRequestWithTypeAll) {
            $userLocations = $isSimilarUserLocationRequestWithTypeAll;
            $_all = $this->get_place_items_by_request_id($isSimilarUserLocationRequestWithTypeAll->id);
            // filter result by type
            $_items = DB::table('rel_search_to_places')
            ->join('places_items', 'places_items.place_id', '=', 'rel_search_to_places.places_id')
            ->where([
                ["rel_search_to_places.user_request_id","=", $isSimilarUserLocationRequestWithTypeAll->id ],
                ["places_items.types", "LIKE", "%".$request['type']."%" ]
            ])
            ->groupBy('places_items.place_id')
            ->select('places_items.*')
            ->get();
            
            return [
                "status"=> "OK", 
                "results" => $_items,
                "referenz" => $isSimilarUserLocationRequestWithTypeAll->id,
                "dev_comment" => "founded in an call with all",
                "cached_data" => "yes"
            ];            
        }

        if(!$userLocations) {                        
            // lege neu an            
            $_data["location"] = [
                "lat" => $request->input("lat"),
                "lng" => $request->input("lng"),
            ];
            $_data["place_id"] = $request->input("placeid");
            $_data["formatted_address"] = $request->input("formattedaddress");            
            $_data["type"] = ($request->input("type")) ? $request->input("type") : "all";
            $_data["radius"] = $request->input("radius");
            // save data
            $new_userLocations_id = $this->saveUserLocaltionRequest($_data);                     

            // start to call google places api by lat lng type
            if ($request->input("type") === 'all') 
            {
                $_places_items = $_places_items_all = $_places_items_merged = [];                
                foreach($this->google_places_api_types as $name) {                                   
                    $places_parameters = [    
                        'type' => $name,
                        'lat' => $request->input("lat"),
                        'lng' => $request->input("lng"),
                        'radius' => $request->input("radius"),                        
                    ];                        
                    // call google places nearby api                        
                    $_places_items_all[] = $this->get_places_data_from_google_api($places_parameters);                    
                }                
                $_places_items_merged = call_user_func_array('array_merge', $_places_items_all);
                $_places_items = $this->helper_array_multi_unique($_places_items_merged);                

                // iterate $_places_items an insert place_id, request_id to the relation table            
                foreach($_places_items as $key => $item) {
                    $_placeItem = PlacesItem::where(["place_id"=>$item['place_id']])->first();
                    $delete_item = false;
                    if($_placeItem) {
                        $delete_item = true;
                    }                
                    DB::table('rel_search_to_places')->insert([
                        'user_request_id' => $new_userLocations_id, 
                        'places_id' => $item['place_id']
                        ]);                
                    if($delete_item) {
                        unset($_places_items[$key]);
                    }
                }

                // call google api details and merge both 
                if (count($_places_items) > 0)
                {
                    $_places_items_with_detials = $this->get_google_places_detials_by_place_id( $_places_items );           
                    // store results in databse
                    array_walk($_places_items_with_detials, function($item) {
                        $this->store_full_dataset( $item );
                    });    
                }                   
                
            } else {
                // start to call google places api by lat lng type        
                $places_parameters = [    
                    'type' => $request->input("type"),
                    'lat' => $request->input("lat"),
                    'lng' => $request->input("lng"),
                    'radius' => $request->input("radius"),                
                ];    
                // call google places nearby api    
                $_places_items = $this->get_places_data_from_google_api($places_parameters);
                
                // iterate $_places_items an insert place_id, request_id to the relation table            
                foreach($_places_items as $key => $item) {
                    $_placeItem = PlacesItem::where(["place_id"=>$item['place_id']])->first();
                    $delete_item = false;
                    if($_placeItem) {
                        $delete_item = true;
                    }                
                    DB::table('rel_search_to_places')->insert([
                        'user_request_id' => $new_userLocations_id, 
                        'places_id' => $item['place_id']
                        ]);                
                    if($delete_item) {
                        unset($_places_items[$key]);
                    }
                }
                // call google api details and merge both 

                if (count($_places_items) > 0)
                {
                    $_places_items_with_detials = $this->get_google_places_detials_by_place_id( $_places_items );           
                    // store results in databse
                    array_walk($_places_items_with_detials, function($item) {
                        $this->store_full_dataset( $item );
                    });    
                }  

            }            

        }
        
        $_id = $chached_data = isset($userLocations->id) ? $userLocations->id : $new_userLocations_id;
        return [
            "status"=> "OK", 
            "results" => $this->get_place_items_by_request_id($_id),
            "referenz" => $_id,
            "cached_data" => $chached_data ? "yes" : "no"
        ];
    }    

    /**
     * Get Place Items by user request id. the user request id referenz to 
     * google maps search id place_id.
     *
     * @param Int $_id
     * @return Array
     */
    private function get_place_items_by_request_id(Int $_id):array  {
        $_items = DB::table('rel_search_to_places')
            ->join('places_items', 'places_items.place_id', '=', 'rel_search_to_places.places_id')
            ->where(["rel_search_to_places.user_request_id" => $_id ])
            ->select('places_items.*')
            ->get();

        // clear array from doubles
        $item = $this->helper_array_multi_unique($_items);
        return $item;
    } 

    /**
     * Finaly store the crawled dataset in the database
     *
     * @param Array $full_dataset
     * @return void
     */
    private function store_full_dataset(Array $full_dataset): void
    {
        // check if dataset allready exists
        if(true === PlacesItem::where([
            "place_id" => $full_dataset["place_id"]
            ])->exists()) {
                // echo '<p>store full:' . $full_dataset["place_id"] . '</p>';
            return ;
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
    public function get_google_places_detials_by_place_id(
        Array $items = [], 
        Array $fields = ["name","rating","formatted_phone_number","website","address_component","adr_address","formatted_address"]
        ): Array
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

    /**
     * Call the google places nearbysearch api 
     *
     * @param Array $request
     * @return Array
     */
    public function get_places_data_from_google_api(Array $request): Array {
        sleep($this->sleeping_in_seconds);        
        $pagetoken = $type = "";
        $pagetoken = isset($request['pagetoken']) ? $request['pagetoken'] : null;
        if ( null !== $request['type'] ) {
            $type = $request['type'];
        } else {
            $type = "bar";
        }
        $radius = isset($request['radius']) ? $request['radius'] : 1500;

        $response = [];   
        $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', 
            [
                'location'=> $request['lat'].",".$request['lng'],
                'radius' => $radius,
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
     * @return Int
     */
    private function saveUserLocaltionRequest(Array $data = NULL): Int
    {           
        $userLocaltionRequest = new UserLocationRequest();
        $userLocaltionRequest->request = isset($data["request"]) ? $data["request"] : "";
        $userLocaltionRequest->formatted_address = $data["formatted_address"];
        $userLocaltionRequest->place_id = $data["place_id"];
        $userLocaltionRequest->location = $data["location"];        
        $userLocaltionRequest->type = $data["type"];     
        $userLocaltionRequest->radius = $data["radius"];
        $userLocaltionRequest->save();       
        return $userLocaltionRequest->id;
    }

    /**
     * Set Sleepingtime between the google nearby places api calls
     * to avoid an google error
     *
     * @param Request $request
     * @return void
     */
    public function set_sleeping_in_seconds(Request $request): void
    {
        if ($request->secounds < 1) {
            $this->sleeping_in_seconds = 1;
        }
        if ($request->secounds > 5) {
            $this->sleeping_in_seconds = 5;
        }        
        $this->sleeping_in_seconds = $request->secounds;
    }

    /**
     * array_unique() für multidimensionale Arrays
     *
     * @param [type] $multiArray
     * @return array
     */
    function helper_array_multi_unique($multiArray): array
    {
        $uniqueArray = array();        
        foreach($multiArray as $subArray){          
          if(!in_array($subArray, $uniqueArray)){            
            $uniqueArray[] = $subArray;
          }
        }
        return $uniqueArray;
    }       
}
