<?php

namespace App\Http\Resources\Payments\Mpesa\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Payments\Mpesa\STKFailedTransactions;

class STKResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id
        ];
    }
}
