<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

    protected $fillable = [
        'sundayS',
        'mondayS',
        'tuesdayS',
        'wednesdayS',
        'thursdayS',
        'sundayE',
        'mondayE',
        'tuesdayE',
        'wednesdayE',
        'thursdayE',
        'expert_id',

    ];
    public function Family_Consultation()
    {
        return $this->hasOne('App\Models\Family_Consultation');
    }
    public function Medical_Consultation()
    {
        return $this->hasOne('App\Models\Medical_Consultation');
    }
    public function Psychological_Consultation()
    {
        return $this->hasOne('App\Models\Psychological_Consultation');
    }
    public function Vocational_Consultation()
    {
        return $this->hasOne('App\Models\Vocational_Consultation');
    }
    public function Business_Management_Consultation()
    {
        return $this->hasOne('App\Models\Business_Management_Consultation');
    }


    public function Expert()
    {
        return $this->belongsTo(Expert::class);
    }


}
