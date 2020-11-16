<?php 
namespace App\Repositories;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\PlacesItem;
use App\Models\RelSearchToPlace;
use App\Models\Setting;
use App\Models\UserLocationRequest;

class PlacesItemRepository {

    /**
     * Places Item Model
     *
     * @var PlacesItem
     */
    protected $places_item;

    /**
     * Google Key. It appending on the request url as parameter.
     *
     * @var string
     */    
    protected $google_api_key;

    /**
     * Cache Duration in days
     *
     * @var string
     */
    protected $cache_duration;

    /**
     * Between every google places nearby bulk api call it is useful
     * to wait any secound before start the next request. Avoiding
     * some problems with google restrictions.
     * 
     * @var integer
     */    
    protected $sleeping_in_seconds = 1;

    /**
     *
     * @var array
     */
    protected $result_array_set = [];

    /**
     * Google Places Types of our interesst
     *
     * @var array
     */
    protected $google_places_api_types = [
        "amusement_park",
        "bar",
        "bowling_alley",
        "cafe",
        "campground",
        "lodging",
        "meal_delivery",
        "meal_takeaway",
        "movie_theater",
        "night_club",
        "restaurant"
    ];    

    public function __construct() {
        $this->google_api_key = env('GOOGLE_MAPS_API_KEY', NULL);      
        $cache_duration = Setting::where('key', 'cache_duration')->first();
        if (!isset($cache_duration->value)) {
            $this->cache_duration = 365;
        } else {
            $this->cache_duration = $cache_duration->value;
        }  
    }

    public function getPlacesItemsByParams(Request $request) {        
        $request->validated();
        
        $userLocations = UserLocationRequest::where([
            ["place_id","=",$request->input("placeid")],
            ["type", "=", $request->input("type")],
            ["radius", "=", $request->input("radius")]
        ])->first();
        
        if ($userLocations) {
            /**
             * check if cache duration expired
             */
            if ($userLocations->updated_at->diffInDays(Carbon::now()) >= $this->cache_duration)
            {
                return $this->updateWholeItemsFromUserLocationRequest($request, $userLocations );
            }  
        }   

        /**
         * Check if there was a similar request (radius, localtion) in 
         * the past but with type = all.
         */
        $isSimilarUserLocationRequestWithTypeAll = UserLocationRequest::where([
            ["place_id","=",$request->input("placeid")],
            ["type", "=", 'all'],
            ["radius", "=", $request->input("radius")]
        ])->first(); 
        if (!$userLocations && $isSimilarUserLocationRequestWithTypeAll) {
            return $this->getDataByTypeFromCachedDataWhereCrawledByTypeAll(
                $request, 
                $isSimilarUserLocationRequestWithTypeAll);
        }
            
        /**
         * create new LocationSearch
         */        
        if(!$userLocations) {                                   
            return $this->startNewLocationSearch($request);
        }        
        
        $_id = $userLocations->id;
        return [
            "status"=> "OK", 
            "results" => $this->get_place_items_by_request_id($_id),
            "referenz" => $_id,
            "dev_comment" => 'Founded User Request and loaded from Cache.',
            "cached_data" => "yes"
        ];            
    }

    /**
     * startNewLocationSearch
     *
     * @param Request $request
     * @return Array
     */
    private function startNewLocationSearch(Request $request): Array {
        $_data["location"] = [
            "lat" => $request->input("lat"),
            "lng" => $request->input("lng"),
        ];
        $_data["place_id"] = $request->input("placeid");
        $_data["formatted_address"] = $request->input("formattedaddress");            
        $_data["type"] = ($request->input("type")) ? $request->input("type") : "all";
        $_data["radius"] = $request->input("radius");
        // create new UserLocationRequest
        $new_userLocations_id = $this->saveUserLocaltionRequest($_data);                     

        // start to call google places api by lat lng type
        if ('all' === $request->input("type")) 
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
                $_places_items_with_details = $this->get_google_places_details_by_place_id( $_places_items );           
                // store results in databse
                array_walk($_places_items_with_details, function($item) {
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

                $rel_search_to_place = new RelSearchToPlace;
                $rel_search_to_place->user_request_id = $new_userLocations_id;
                $rel_search_to_place->places_id = $item['place_id'];
                $rel_search_to_place->save();

                if($delete_item) {
                    unset($_places_items[$key]);
                }
            }
            // call google api details and merge both 

            if (count($_places_items) > 0)
            {
                $_places_items_with_details = $this->get_google_places_details_by_place_id( $_places_items );           
                // store results in databse
                array_walk($_places_items_with_details, function($item) {
                    $this->store_full_dataset( $item );
                });    
            }  

        }
        $_id = $new_userLocations_id;
        return [
            "status"=> "OK", 
            "results" => $this->get_place_items_by_request_id($_id),
            "referenz" => $_id,
            "dev_comment" => 'New data catched from latest google places api call.',
            "cached_data" => "no"
        ];                    
    
    }    

    /**
     * updateWholeItemsFromUserLocationRequest
     *
     * @param Request $request
     * @param Array $userLocations
     * @return Array
     */
    private function updateWholeItemsFromUserLocationRequest(
        Request $request, 
        Object $userLocations ) : Array
    {   
        // start to call google places api by lat lng type and updated the cache
        if ('all' === $request->input("type")) {
            // @TODO write the loop for all            
            return [
                "status"=> "OK", 
                "results" => $this->get_place_items_by_request_id($userLocations['id']),
                "referenz" => $_id,
                "dev_comment" => 'Cached data but not updated by request with type all. @todo',
                "cached_data" => "yes"
            ];               

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
        }
        
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
            if($_placeItem) 
            {
                if ($_placeItem->updated_at->diffInDays(Carbon::now()) >= $this->cache_duration) 
                {                             
                    // this item has to be crawled new because duration time expired                    
                    $delete_item = false;
                } else {
                    $delete_item = true;
                }                                                                              
                // refresh the updated_at field in the relationSearchToPlace row
                $relSearchToPlaceModel = RelSearchToPlace::where([
                    ['user_request_id','=',$userLocations->id],
                    ['places_id','=',$item['place_id']]
                ])->first();                                        
                $relSearchToPlaceModel->touch();                
                if($delete_item) {
                    unset($_places_items[$key]);                    
                    continue;
                }
                // call details for this item
                // call google api details and merge both (places with details data)                
                $_places_item_with_details = $this->get_google_places_details_by_place_id( 
                    [$item] , 
                    ["name","rating","formatted_phone_number","website","address_component","adr_address","formatted_address","business_status"], 
                    true
                );
                // store results in databse
                array_walk($_places_item_with_details, function($full_dataset) use ($request) {                            
                    $p = PlacesItem::updateOrCreate(
                        [ 'place_id' => $full_dataset['place_id'] ],
                        [
                            'name' => $full_dataset["name"],
                            'types' => implode(",",$full_dataset["types"]),
                            'location' => $full_dataset["geometry"]["location"],
                            'place' => $full_dataset["place"],
                            'zip' => $full_dataset["zip"],
                            'street' => $full_dataset["street"],
                            'street_number' => $full_dataset["street_number"],
                            'country' => $full_dataset["country"],
                            'phone' => $full_dataset["phone"],
                            'website' => $full_dataset["website"],
                            'formatted_address' => $full_dataset["formatted_address"],
                            'user_ratings_total' => isset($full_dataset["user_ratings_total"]) ? $full_dataset["user_ratings_total"] : 0                                    
                        ]
                    );
                    if ( !$p->getChanges() ) {                                
                        echo '<pre>array_walk';print_r("noc changes");echo'</pre>';
                        $p->touch();                                
                    }                                
                });                            

            } else {
                // create new PlacesItem Entrie
                $_places_items_with_details = $this->get_google_places_details_by_place_id( [$item] );           
                // store results in databse
                array_walk($_places_items_with_details, function($item) {                            
                    $this->store_full_dataset( $item );
                });                           

                // insert new RelSearchToPlace Row because it will an new entrie
                $rel_search_to_place = new RelSearchToPlace;
                $rel_search_to_place->user_request_id = $userLocations->id;
                $rel_search_to_place->places_id = $item['place_id'];
                $rel_search_to_place->save();

            }      
            
        }
        // update the update_at field in userLocation Model
        $updateModel = UserLocationRequest::find($userLocations->id);
        $updateModel->touch();

        // get items by userLocationId
        $_id = $userLocations->id;
        return [
            "status"=> "OK", 
            "results" => $this->get_place_items_by_request_id($_id),
            "referenz" => $_id,
            "dev_comment" => 'Yes and no. Cache duration time expired. updated items.',
            "cached_data" => "yes,no"
        ];   
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
     * Call the google places api for recieving detailinformation about this place.
     * Passing two arrays. First is the resultset from the previews google places api call.
     * the secound parameter specified/descript what we are would like to receive.
     * After receiving data this function merge both two on.
     * 
     * @param Array $items
     * @param Array $fields
     * @param Bool $is_cached_duration_time_expired
     * @return Array
     */
    public function get_google_places_details_by_place_id(
        Array $items = [], 
        Array $fields = ["name","rating","formatted_phone_number","website","address_component","adr_address","formatted_address","business_status"],
        Bool $is_cached_duration_time_expired = false
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
                if (!$is_cached_duration_time_expired) {
                    echo "<p>".$item["place_id"]."</p>";
                    continue;
                }
            }

            $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json',[
                    'place_id' => $item['place_id'], 
                    'fields' => $fields,
                    'key' => $this->google_api_key
                    ]);
            $results[$key] = $response->json();
            $items[$key]["name"] = ( isset($response->json()['result']['name']) ?  
                $response->json()['result']['name'] : NULL );
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
     * Filter the passed array by passed name.
     *
     * @param Array $dataset
     * @param String $name
     * @return NULL || string
     */
    private function filter_value_from_google_address_components_by_name(Array $dataset, String $name): ?string 
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
     * Get Place Items by user request id. the user request id referenz to 
     * google maps search id place_id.
     *
     * @param Int $_id
     * @return Array
     */
    private function get_place_items_by_request_id(Int $_id): Array  {
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
     * getDataByTypeFromCachedDataWhereCrawledByTypeAll
     *
     * @param Request $request
     * @param object $isSimilarUserLocationRequestWithTypeAll
     * @return array
     */
    private function getDataByTypeFromCachedDataWhereCrawledByTypeAll(
        Request $request, 
        object $isSimilarUserLocationRequestWithTypeAll
        ): array 
    {
        $userLocations = $isSimilarUserLocationRequestWithTypeAll;
        #$_all = $this->get_place_items_by_request_id($isSimilarUserLocationRequestWithTypeAll->id);
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
        // cache duration check       
        
        return [
            "status"=> "OK", 
            "results" => $this->cache_duration_check( $_items->all() ), #$_items,
            "referenz" => $isSimilarUserLocationRequestWithTypeAll->id,
            "dev_comment" => "Founded in an call with all. It was checked cache duration. If cache duration expired you'll found the updated items with the flag updated and true.",
            "cached_data" => "yes"
        ];  
    }
    
    /**
     * Pass an Array of items to check if the cache duration time is 
     * bigger than update value from the item.
     *
     * @param Array $items
     * @return Array
     */
    protected function cache_duration_check(Array $items): Array {
        // iterate items and collect all items where update date older as cache durration
        #$items= array_chunk($items, 2)[0];     
        $itemsChecked = [];     
        foreach($items as $key => $item) {              
            if ( Carbon::create($item->updated_at)->diffInDays(Carbon::now()) >= $this->cache_duration ) 
            {
                // start an new google detail api call with the collected data
                $new_item = [];
                $new_item = $this->get_google_places_details_by_place_id( 
                    [ (array) $item], 
                    ["name","rating","formatted_phone_number","website","address_component","adr_address","formatted_address","business_status"], 
                    true);                
                // update the rows                        
                unset($new_item[0]['adr_address']);                     
                $pi = PlacesItem::find($new_item[0]['id']);
                $pi->update($new_item[0]);
                $pi->touch(); // $pi->fresh()->toArray();                    
                $_new_item = (PlacesItem::find($new_item[0]['id']))->toArray();
                $_new_item['updated'] = true;
                $itemsChecked[] = (object) $_new_item;
            } else {
                $itemsChecked[] = $item;
            }
        }              
        return $itemsChecked;
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
                 #echo '<p>store full:' . $full_dataset["place_id"] . '</p>';
            return ;
        }
        $new_item = new PlacesItem();

        $new_item->place_id = $full_dataset["place_id"];
        $new_item->name = $full_dataset["name"];
        $new_item->types = implode(",",$full_dataset["types"]);
        $new_item->location = json_encode($full_dataset["geometry"]["location"]);
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
     * array_unique() für multidimensionale Arrays
     *
     * @param [type] $multiArray
     * @return Array
     */
    protected function helper_array_multi_unique($multiArray): Array
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
