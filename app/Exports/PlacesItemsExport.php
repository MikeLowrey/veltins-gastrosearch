<?php

namespace App\Exports;

#use Maatwebsite\Excel\Concerns\FromCollection;
#use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Support\Facades\DB;

class PlacesItemsExport implements FromArray, WithMapping, WithHeadings
{
    protected $places_item;
    
    public function __construct(array $places_item) {
        $this->places_item = $places_item;        
    }

    public function array(): array
    {
         return $this->places_item;
         
    }    

    /**
     * Map the fields / columns
     *
     * @param [object] $places_item
     * @return array
     */
    public function map($places_item): array
    {
        return [
            '',
            $places_item->name,
            '',
            $places_item->street . ' ' .$places_item->street_number,
            $places_item->zip,
            $places_item->place,
            '',
            '',
            'Deutschland',
            'D',
            $places_item->phone,
            '',
            '',
            '',
            '',
            $places_item->place_id,
            '',
            '',
            '',
            '',
            '',
        ];
    }   
    
    /**
     * Table Headers
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'KUNNR',
            'Name1',
            'Name2',
            'STRAS',
            'PSTLZ',
            'ORT01',
            'sortl',
            'Brsch',
            'land',
            'Spras',
            'TELF1',
            'TELFX',
            'GLN',
            'Kndr Veltins',
            'Prolle',
            'Trade Komplett.KUNNAR',
            'BETRFORM',
            'VKFL',
            'Bemerkung Hierachie',
            'Unterzentrale',
            'Bemerkung Unterzentrale',
        ];
    }
}
