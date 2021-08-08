<?php

namespace App\Http\Resources\Payments\Mpesa\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class STKFailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
        // return [
        //     'id' => $this->id
        // ];
    }
}
