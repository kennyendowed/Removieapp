<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AllDepositResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return[
        //     'id' => $this->id,
        //     'deposit_number' => $this->deposit_number,
        //     'user_id' => $this->user_id,
        //     'plan_id' => $this->plan_id,
        //     'percent' => $this->percent,
        //     'time' => $this->time,
        //     'compound_id' => $this->compound_id,
        //     'amount' => $this->amount,
        //     'status' => $this->status,
        //     'created_at' => (string) $this->created_at,
        //     'updated_at' => (string) $this->updated_at,
        // ];
         return parent::toArray($request);
    }
}
