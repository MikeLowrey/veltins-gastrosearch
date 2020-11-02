<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PlacesItem;
use \App\Models\UserLocationRequest;
use \App\Models\RelSearchToPlace;
use \App\Http\Requests\RequestGeoDataForGooglePlacesCall;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class PageController extends Controller
{
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
    }    
}