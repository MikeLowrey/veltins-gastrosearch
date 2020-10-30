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
    public function search_by_zip(Request $request): object 
    {        
        if (!preg_match( '/^\d{2}\d{0,3}$/', $request->zip )) {            
            return response(["status"=>"not found"],422);
        }             
        //@todo if nothing found call geolocater and after that call the google places api for possible results   
        
        return response([
            'status' => 'OK',
            'results' => PlacesItem::where([['zip', 'like', $request->zip.'%']])->get(),            
        ]);        
    }    

    /**
     * Search Database by Zip and Type
     *
     * @param Request $request
     * @return array
     */
    public function search_by_zip_and_type(Request $request): object {        
        if (!preg_match( '/^\d{2}\d{0,3}$/', $request->zip )) {            
            return response(["status"=>"not found"],422);
        }     

        if ($request->type === 'all') {
            $types_eloqent_condition = [
                ['zip', 'like', $request->zip.'%'],                
            ];
        } else {
            $types_eloqent_condition = [
                ['zip', 'like', $request->zip.'%'],
                ['types', 'like', '%'.$request->type.'%']
            ];
        }        
        //@todo if nothing found call geolocater and after that call the google places api for possible results   
        $collection = PlacesItem::where($types_eloqent_condition)->get();    

        return response([
            'status' => 'OK',
            'results' => $collection
        ]);        
    }
}
