<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Setting;

class SettingController extends Controller
{
    private $defaults = [
        'file_format' => ["file_format", "csv"] // 'file_format' => 'csv',                
    ];

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
     * Restore Defaults in Database. 
     * Initial 
     *
     * @return void
     */
    protected function restore_defaults(): void 
    {
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
}
