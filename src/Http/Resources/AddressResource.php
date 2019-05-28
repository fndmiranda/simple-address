<?php

namespace Fndmiranda\SimpleAddress\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'postcode' => $this->postcode,
            'neighborhood_id' => $this->neighborhood_id,
            'neighborhood' => NeighborhoodResource::make($this->whenLoaded('neighborhood')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'pivot' => $this->whenPivotLoaded('address_addressables', function () {
                return [
                    'number' => $this->pivot->number,
                    'complement' => $this->pivot->complement,
                    'lat' => $this->pivot->lat,
                    'lng' => $this->pivot->lng,
                    'created_at' => $this->pivot->created_at->toIso8601String(),
                    'updated_at' => $this->pivot->updated_at->toIso8601String(),
                ];
            }),
        ];
    }
}
