<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Expert as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Expert extends User implements JWTSubject
{
    use HasFactory, Notifiable;
    use HasApiTokens ;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phonenumber',
        'address',
        'experinces',
        'user_id',
        'wallet',
        'value_of_rating',
        'imgpath',


        // 'M',
        // 'P',
        // 'V',
        // 'B',
        // 'F',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'M'=>'boolean',
        'P'=>'boolean',
        'V'=>'boolean',
        'B'=>'boolean',
        'F'=>'boolean',
        //'value_of_rating' => 'floatval',
        //'value_of_rating' =>'double' ,


    ];

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
        return [];
    }
    public function Business_Management_Consultation(){
        return $this->hasOne('App\Models\Business_Management_Consultation');
    }
    public function Family_Consultation(){
        return $this->hasOne('App\Models\Family_Consultation');
    }
    public function Vocational_Consultation(){
        return $this->hasOne('App\Models\Vocational_Consultation');
    }
    public function Psychological_Consultation(){
        return $this->hasOne('App\Models\Psychological_Consultation');
    }
    public function Medical_Consultation(){
        return $this->hasOne('App\Models\Medical_Consultation');
    }

    public function Time(){
        return $this->hasOne('App\Models\Time');
    }

    public function Consultation(){
        return $this->hasOne('App\Models\Consultation');
    }
    public function Time_Management(){
        return $this->hasMany('App\Models\Time_Management');
    }


    public function Reserved_Time(){
        return $this->hasMany('App\Models\Reserved_Time');
    }

    public function Favourite(){
        return $this->hasMany('App\Models\Favourite');
    }
    public function Message(){
        return $this->hasMany('App\Models\Message');
    }
    public function Chat(){
        return $this->hasMany('App\Models\Chat');
    }
    public function scopeMember($query){
        return $query->where('user_id','<>',2);
    }

}
