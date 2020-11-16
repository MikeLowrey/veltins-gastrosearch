<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Setting;

class SettingController extends Controller
{
    protected $defaults = [
        'file_format' => ["file_format", "csv"], // 'file_format' => 'csv',                
        'cache_duration' => ["cache_duration","365"],
        'sleeptime_google_api' => ["sleeptime_google_api","1"],
    ];    

    /**
     * Set in database the cache duration in days 
     *
     * @param Request $request
     * @return void
     */
    public function set_cache_duration(Request $request): void
    {   
        if (!filter_var(
            $request->days, 
            FILTER_VALIDATE_INT, 
            array(
                'options' => array(
                    'min_range' => "0", 
                    'max_range' => "730"
                )
            )
        ) ) {
            abort(404);
        }       
           
        if (!Setting::where('key', 'cache_duration')->first()) {
            $this->set_defaults_by_key('cache_duration');
        }
        if ( ($request->days) ) {            
            Setting::where('key', 'cache_duration')            
            ->update(['value' => $request->days]);            
        }
    }

    /**
     * Setter File Format
     * PUT /api/settings/file_format/xlsx
     * 
     * @param Request $request
     * @return void
     */
    public function set_file_format(Request $request): void {
        if (!Setting::where('key', 'file_format')->first()) {
            $this->set_defaults_by_key('file_format');
        }
        if ( in_array($request->ext, ["csv", "xlsx"]) ) {            
            Setting::where('key', 'file_format')            
            ->update(['value' => $request->ext]);            
        }       
    }

    /**
     * Set Sleepingtime between the google nearby places api calls
     * to avoid an google error
     *
     * @param Request $request
     * @return void
     */
    public function set_sleeptime_google_api(Request $request): void
    {
        if ($request->sleeptime_google_api < 1) {
            $this->sleeptime_google_api = 1;
        }
        if ($request->sleeptime_google_api > 5) {
            $this->sleeptime_google_api = 5;
        }        
        $this->sleeptime_google_api = $request->sleeptime_google_api;
    }      

    /**
     * Restore Defaults in Database. 
     * Initial 
     *
     * @return void
     */
    protected function restore_defaults(): void 
    {
        Setting::query()->delete();
        foreach ($this->defaults as $key => $value) 
        {
            Setting::create([
                'key' => $this->defaults[$key][0],
                'value' => $this->defaults[$key][1],
            ]);            
        }        
        #Setting::where('key', 'file_format')            
        #->update(['value' => $this->file_format]);                   
    }

    /**
     * Set default Value by Key function
     *
     * @param String $key
     * @return void
     */
    public function set_defaults_by_key(String $key): void {
        Setting::create([
            'key' => $this->defaults[$key][0],
            'value' => $this->defaults[$key][1],
        ]);
    }

    /**
     * getter for all settings
     *
     * @return array
     */
    public function get_settings(): array {
        $arr = (Setting::all());
        $settings_array = [];
        foreach($arr as $k => $v) {            
            $settings_array[$v['key']] = $v['value'];
        }
        return $settings_array;
    }
}
