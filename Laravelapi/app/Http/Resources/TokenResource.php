<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
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
            "country"=> $this->country,
            "ip_address"=> $this->ip_address,
            "updated_at" => (string) $this->created_at,
            "created_at" => (string) $this->updated_at,
            "user_id"=> $this->id,
            "email_verificationStatus"=> $this->email_verify,
            "address"=> $this->address,
            "amount"=> $this->amount,
            "account"=> $this->account,
            "BankName"=> $this->BankName,
            "AccountName"=> $this->AccountName,
            "AccountNumber"=> $this->AccountNumber,
            "Access_type"=> $this->is_permission,
            "profile_status"=> $this->status,
            "avater"=> $this->image,
            "email_verify"=> $this->email_verify,
            "email_verified_at"=> $this->email_verified_at,
            "email_time"=> $this->email_time,
            "email_code"=> $this->email_code,
            "block_status"=> $this->block_status,
            "block_at"=> $this->block_at,
    
            
          ];
    }
}
