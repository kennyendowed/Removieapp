<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
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
        "Full_name"=> $this->name,
        "email"=> $this->email,
        "phone_number"=> $this->phone,
        "ip_address"=> $this->ip_address,
        'qrcode'=>$this->qrcode,
        "updated_at" => (string) $this->created_at,
        "created_at" => (string) $this->updated_at,
        "user_id"=> $this->id,
        "activitie"=> $this->user_activities,
        "email_verificationStatus"=> $this->email_verify,
        'message'=>[[
          'success' => true,
          'message' => 'Created Successfully',
          'status' => '200'
        ]]

        
      ];
     // return parent::toArray($request);
    }
}
