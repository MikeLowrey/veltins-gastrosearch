<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PlacesItem;
use \App\Models\UserLocationRequest;
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

    public function __construct() {}

    /**
     * @todo delete this one. only for testing
     *
     * @return void
     */
    public function test() {        
        return PlacesItem::where("zip","=","59939")->get();
        #dd( new PlacesItems(PlacesItem::find(21)) );
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return object
     */
    public function export_by_zip_and_type(Request $request): object
    {
        if (!preg_match( '/\d{5}/', $request->zip )) {   
            return abort(404);         
            return response(["status"=>"not founded"],422);
        }   
        $file_name = $request->zip ."_" . Str::snake($request->type) . ".". $this->file_format;
        
        $collection = PlacesItem::where([    
                ['zip', '=', $request->zip],
                ['types', 'like', '%'.$request->type.'%']                
            ])->get();
        $export = new PlacesItemsExport($collection->all());
        return Excel::download($export, $file_name);         
    }

    /**
     * make tablesheet
     *
     * @param Request $request
     * @return object
     */
    public function export(Request $request): object
    {
        $user_location_request = UserLocationRequest::find($request->id);            
        if(!$user_location_request ) 
        {            
            return ["status"=> "ID not founded"];
        }
        $umlaute = ["Ä" => "AE", "Ö" => "OE", "Ü" => "UE", "ä" => "ae", "ö" => "oe", "ü" => "ue"];
        $file_name = Str::slug(strtr($user_location_request->formatted_address, $umlaute))."." . $this->file_format;
        
        $collection = DB::table('rel_search_to_places')        
            ->join('places_items', 'places_items.place_id', '=', 'rel_search_to_places.places_id')
            ->where(["rel_search_to_places.user_request_id" => $user_location_request->id ])
            ->select('places_items.*')
            ->get();                
             
        $export = new PlacesItemsExport($collection->all());
        return Excel::download($export, $file_name);  
    }  

    /**
     * File format setter
     *
     * @param Request $request
     * @return void
     */
    public function set_file_format(Request $request): void
    {
        $this->file_format = $request->fileformat;
    }
}
