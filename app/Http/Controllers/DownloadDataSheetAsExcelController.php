<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PlacesItem;
use \App\Models\UserLocationRequest;
use \App\Models\Setting;
use \App\Exports\PlacesItemsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use \App\Http\Resources\PlacesItems;
use Illuminate\Support\Str;


class DownloadDataSheetAsExcelController extends Controller 
{
    /**
     * File format for response. csv or xlsx
     *
     * @var string
     */
    private $file_format = "csv"; //  XLSX

    public function __construct() {
        $file_format = Setting::where(["key"=>"file_format"])->first();
        $this->file_format = $file_format->value;
    }

    public function test(Request $request) {
        return abort(404);   
    }
    /**
     * Make tablesheet by zip function
     *
     * @param Request $request
     * @return object
     */
    public function export_by_zip_and_type(Request $request): object
    {        
        if (!preg_match( '/^\d{2}\d{0,3}$/', $request->zip )) {   
            return abort(404);         
            return response(["status"=>"not founded"],422);
        }
        $file_name = $request->zip ."_" . Str::snake($request->type) . ".". $this->file_format;
        
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
        
        $collection = PlacesItem::where($types_eloqent_condition)->get();            
        $export = new PlacesItemsExport($collection->all());
        return Excel::download($export, $file_name);         
    }

    /**
     * make tablesheet by user request id
     *
     * @param Request $request
     * @return object
     */
    public function export(Request $request): object
    {        
        $user_location_request = UserLocationRequest::find($request->id);            
        if(!$user_location_request ) 
        {            
            return response(["status"=> "ID not founded"]);
        }
        $umlaute = ["Ä" => "AE", "Ö" => "OE", "Ü" => "UE", "ä" => "ae", "ö" => "oe", "ü" => "ue"];
        $file_name = Str::slug(strtr($user_location_request->formatted_address, $umlaute))."." . $this->file_format;
        
        $_collection = DB::table('rel_search_to_places')        
            ->join('places_items', 'places_items.place_id', '=', 'rel_search_to_places.places_id')
            ->where(["rel_search_to_places.user_request_id" => $user_location_request->id ])
            ->select('places_items.*')
            ->get();                

        //ereas all doubles from resultlist
        $collection = $this->helper_array_multi_unique($_collection);

        // filter by type        
        if ($request->type && $request->type != 'all') {
            $_type = $request->type;
            $collection = array_filter($collection, function($item) use ($_type) {                
                return strpos($item->types, $_type) !== false ? true: false;
            });                        
        }       

        $export = new PlacesItemsExport($collection);
        return Excel::download($export, $file_name);  
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

    /**
     * File format setter
     *
     * @param Request $request
     * @return void
     */
    public function set_file_format(Request $request): void
    {
        $this->file_format = $request->ext;
    }
}
