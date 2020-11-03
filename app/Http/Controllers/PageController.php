<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PlacesItem;
use \App\Models\UserLocationRequest;
use \App\Models\RelSearchToPlace;
use \App\Http\Requests\RequestGeoDataForGooglePlacesCall;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use \App\Models\Setting;

class PageController extends Controller
{
    public function statistics() {
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
        return view('statistics')->with('data',$data);
    }    

    /**
     * Settings
     *
     * @return View
     */
    public function settings(): Object {
        $data = Setting::all();
        $versions = shell_exec('git log --pretty=oneline');
        $pattern = '/[a-f0-9]{40}/i';                
        $_str = preg_replace($pattern, '###', $versions);
        $versions = explode('###',$_str);     
        return view('settings')->with('data',$data)->with('versions',$versions);
    }
    
}