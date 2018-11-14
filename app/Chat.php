<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public function messages()
    {
        return $this->hasMany('App\Message' , 'chat_id');
    }
}
