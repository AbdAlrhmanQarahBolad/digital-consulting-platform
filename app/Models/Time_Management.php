<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time_Management extends Model
{
    use HasFactory;
    protected $fillable = [
    'expert_id',
    'num_of_day',
    'start',
    'end',
    ];




    public function Expert()
    {
        return $this->belongsTo(Expert::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
