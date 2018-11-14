<?php

use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/

Websocket::on('connect', function ($websocket, Request $request) {
    Websocket::loginUsingId($request->user()->id);
    echo "user with id: ".$request->user()->id." connected \n"; 
});

Websocket::on('disconnect', function ($websocket) {
    // called while socket on disconnect
});

Websocket::on('send_msg' , 'App\Http\Controllers\ChatController@emit_message');

Websocket::on('validation_error' , 'App\Http\Controllers\ChatController@emit_error');

Websocket::on('join_room' , 'App\Http\Controllers\ChatController@join_room');

Websocket::on('example', function ($websocket, $data) {
    $websocket->emit('message', $data);
});

// Websocket::on('test', 'ExampleController@method');