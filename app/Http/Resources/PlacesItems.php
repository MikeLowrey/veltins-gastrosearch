<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @todo: develop an better solution for collection maping / model mutator
 */
class PlacesItems extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'KUNNR' => '',
            'Name1' => $this->id,
            'Name2' => $this->name,
            'STRAS' => $this->street . ' ' .$this->street_number,
            'PSTLZ' => $this->zip,
            'ORT01' => $this->place,
            'sortl' => '',
            'Brsch' => '',
            'land' => "Deutschland",
            'Spras' => 'D',
            'TELF1' => $this->place_id,
            'TELFX' => '',
            'GLN' => '',
            'Kndr Veltins' => '',
            'Prolle' => '',
            'Trade Komplett.KUNNAR' => $this->place_id,
            'BETRFORM' => '',
            'VKFL' => '',
            'Bemerkung Hierachie' => '',
            'Unterzentrale' => '',
            'Bemerkung Unterzentrale' => '',


           
        ];
        return parent::toArray($request);
    }
}
