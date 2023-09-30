<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'expert_id',

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
