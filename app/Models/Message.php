<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'expert_id',
        'message',
        'from_user' ,
        'from_expert',
        'chat_id',


    ];

    public function Expert()
    {
        return $this->belongsTo(Expert::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function Chat()
    {
        return $this->belongsTo(Chat::class);
    }




}
