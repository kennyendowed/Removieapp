<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UpcomingmovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        dd($request);
        return [
            "adult"=> $this->adult,
            "backdrop_path"=> $this->backdrop_path,
            "genre_ids"=> $this->genre_ids,          
            "id"=>  $this->id,
            "original_language"=>  $this->original_language,
            "original_title"=>  $this->original_title,
            "overview"=>  $this->overview,
            "popularity"=> $this->popularity,
            "poster_path"=> $this->poster_path,
            "release_date"=>  $this->release_date,
            "title"=> $this->title,
            "video"=>  $this->video,
            "vote_average"=> $this->vote_average,
            "vote_count"=>  $this->vote_count,
            'message'=>[[
                'success' => true,
                'message' => 'Successfully',
                'status' => '200'
              ]]
        ];
    }
}
