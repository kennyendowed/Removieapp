<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\user_activities;
use App\Notifications\ResetPassword;
use App\Models\customer;

class User extends Authenticatable implements JWTSubject
{
    use  Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name', 'email','avater','password','phone','address','is_permission','verifyToken','status','email_time','email_code','email_verify','ip_address','qrcode','wristband_id'
    ];

    protected $casts =[
        'user_activities' => 'array'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function letters()
    {
        return $this->belongsToMany('App\Letter');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }


    /**
     * A user can have as many books as possible
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function activities()
    {
        return $this->hasMany(user_activities::class, 'user_id','qrcode');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
      $user =array(
          'id' => $this->id,
        'name' =>$this->name,
         'email' =>$this->email,
         'phone'=>$this->phone,
         'is_permission'=>$this->is_permission,
          'ip_address'=>$this->ip_address
      );

        return [
          'user'=> $user
        ];
    }



}
