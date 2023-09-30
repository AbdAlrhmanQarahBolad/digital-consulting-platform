<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserved_Time extends Model
{
    use HasFactory;

    protected $fillable = [
    'start_of_time_reserved',
    'end_of_time_reserved',
    'number_of_day',
    'expert_id',
    'user_id',
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
