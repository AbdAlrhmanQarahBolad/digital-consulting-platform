<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business_Management_Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_id',
        'consultation_id',
        'time_of_consultaion_b',
        'cost_b',
    ];

    public function Expert()
    {
        return $this->belongsTo(Expert::class);
    }
    public function Time()
    {
        return $this->belongsTo(Time::class);
    }
    public function Consultation(){
        return $this->belongsTo(Consultation::class);

    }
    // public function Expert()
    // {
    //     return $this->hasOne('App\Models\Expert');
    // }



}
