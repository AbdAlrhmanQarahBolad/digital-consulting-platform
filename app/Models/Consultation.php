<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_id',
        'p_id',
        'f_id',
        'b_id',
        'v_id',
        'expert_id',

    ];


    public function Family_Consultation()
    {
        return $this->hasOne(Family_Consultation::class);
    }
    public function Medical_Consultation()
    {
        return $this->hasOne(Medical_Consultation::class);
    }
    public function Psychological_Consultation()
    {
        return $this->hasOne(Psychological_Consultation::class);
    }
    public function Vocational_Consultation()
    {
        return $this->hasOne(Vocational_Consultation::class);
    }
    public function Business_Management_Consultation()
    {
        return $this->hasOne(Business_Management_Consultation::class);
    }
    public function Expert()
    {
        return $this->belongsTo(Expert::class);
    }
    // public function Family_Consultation()
    // {
    //     return $this->hasOne('App\Models\Family_Consultation');
    // }
    // public function Medical_Consultation()
    // {
    //     return $this->hasOne('App\Models\Medical_Consultation');
    // }
    // public function Psychological_Consultation()
    // {
    //     return $this->hasOne('App\Models\Psychological_Consultation');
    // }
    // public function Vocational_Consultation()
    // {
    //     return $this->hasOne('App\Models\Vocational_Consultation');
    // }
    // public function Business_Management_Consultation()
    // {
    //     return $this->hasOne('App\Models\Business_Management_Consultation');
    // }
}


