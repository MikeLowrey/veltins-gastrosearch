<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Models\PlacesItem;

class DatabasePlacesItemsSearchController extends Controller
{
    /**
     * Search Databasse by Zip
     *
     * @param Request $request
     * @return array
     */
    public function search_by_zip(Request $request): object {        
        if (!preg_match( '/\d{5}/', $request->zip )) {            
            return response(["status"=>"not found"],422);
        }     
        //@todo if nothing found call geolocater and after that call the google places api for possible results   

        return response([
            'status' => 'OK',
            'results' => PlacesItem::where("zip","=",$request->zip)->get()
        ]);        
    }    

    /**
     * Search Database by Zip and Type
     *
     * @param Request $request
     * @return array
     */
    public function search_by_zip_and_type(Request $request): object {        
        if (!preg_match( '/\d{5}/', $request->zip )) {            
            return response(["status"=>"not found"],422);
        }     
        //@todo if nothing found call geolocater and after that call the google places api for possible results   
        $collection = PlacesItem::where([    
            ['zip', '=', $request->zip],
            ['types', 'like', '%'.$request->type.'%']                
        ])->get();    

        return response([
            'status' => 'OK',
            'results' => $collection
        ]);        
    }        

}
