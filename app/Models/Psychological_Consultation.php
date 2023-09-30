<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psychological_Consultation extends Model
{
    use HasFactory;
    protected $fillable = [
        'expert_id',
        'consultation_id',
        'time_of_consultaion_p',
        'cost_p',
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
