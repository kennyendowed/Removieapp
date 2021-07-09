<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class user_activities extends Model
{
    protected $table = 'user_activities';

    protected $fillable = ['user_id','description','status'];
    protected $casts =[
        'description' => 'array'
    ];

    public function author()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    
}
