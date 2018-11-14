<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function chat()
    {
        return $this->belongsTo('App\Chat' , 'chat_id');
    }
}
