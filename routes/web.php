<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/start-chat' , 'ChatController@start_chat');
Route::post('/store-chat' , 'ChatController@store_chat');
Route::get('/chatroom/{chatid}' , 'ChatController@chatroom');
Route::post('/store-message' , 'ChatController@store_message');
