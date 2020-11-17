<?php
namespace App\Repositories;


class GooglePlacesRepository {

    
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
}