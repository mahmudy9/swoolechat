<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use App\Chat;
use App\Message;
use App\Events\ErrorMessage;
use App\Events\MessageEvent;
use App\Events\ChatEvent;
use SwooleTW\Http\Websocket\Facades\Websocket;
use SwooleTW\Http\Websocket\Facades\Room;

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function start_chat()
    {
        $users = User::where('id' , '!=' , auth()->user()->id)->get();
        return view('createchat' , compact('users'));
    }

    public function store_chat(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'user2' => 'required'
        ]);
        if($validator->fails())
        {
            return back()->withErrors($validator);
        }
        if(!User::find($request->input('user2')))
        {
            $request->session()->flash('error' , 'invalid user to chat');
            return back();
        }
        if(Chat::where('user1_id' , auth()->user()->id)->where('user2_id' , $request->input('user2'))->exists())
        {
            $chatid = Chat::where('user1_id' , auth()->user()->id)->where('user2_id' , $request->input('user2'))->pluck('id');
            return redirect('chatroom/'.$chatid[0]);
        }

        if(Chat::where('user1_id' ,$request->input('user2'))->where('user2_id',auth()->user()->id)->exists())
        {
            $chatid2 = Chat::where('user1_id' ,$request->input('user2'))->where('user2_id',auth()->user()->id)->pluck('id');
            return redirect('chatroom/'.$chatid2[0]);
        }

        $chat = new Chat;
        $chat->user1_id = auth()->user()->id;
        $chat->user2_id = $request->input('user2');
        $chat->save();
        return redirect('chatroom/'.$chat->id);
    }

    public function chatroom($chatid)
    {
        $chat = Chat::find($chatid);
        if($chat['user1_id'] != auth()->user()->id && $chat['user2_id'] != auth()->user()->id )
        {
            return abort(404);
        }
        if($chat['user1_id'] == auth()->user()->id)
        {

            $to = User::find($chat['user2_id']);
        } else {
            $to = User::find($chat['user1_id']);
        }
        $messages = Message::where('chat_id' , $chatid)->get();
        $from = auth()->user();
        //dd($messages);
        return view('chatroom' , compact('messages' , 'to', 'from' , 'chatid'));
    }


    public function store_message(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'chatid' => 'required',
            'body' => 'required'
        ]);

        if($validator->fails())
        {

            return response()->json(['errors' => $validator->errors()] , 400);
        }

        if(!Chat::find($request->input('chatid')))
        {


            return response()->json(['error'=>'error chat not found'] , 400);
        }

        $chat = Chat::find($request->chatid);

        if($chat['user1_id'] == auth()->user()->id)
        {
            $to = $chat['user2_id'];
        } elseif($chat['user2_id'] == auth()->user()->id)
        {
            $to = $chat['user1_id'];
        }
        //dd($to);
        $message = new Message;
        $message->chat_id = $request->chatid;
        $message->from = auth()->user()->id;
        $message->to = $to;
        $message->fromusername= auth()->user()->name;
        $message->tousername = User::find($to)->name;
        $message->body = $request->body;
        $message->save();
        return $message;
    }

    public function emit_message($websocket ,$request)
    {
        $websocket->emit('receive_msg' , $request['data']['data']);
        
        $websocket->broadcast()->to($request['room'])->emit('reply' , $request['data']['data']);
        //var_dump($request['data']['data']);
    }

    public function emit_error($websocket ,$request)
    {
        $websocket->emit('error_msg' , $request['data']['response']['data']);
        //var_dump($request['data']['response']['data']);
    }

    public function join_room($websocket , $request)
    {
        $rooms = Room::getRooms(Websocket::getUserID());
        if(count($rooms) > 0){
            Room::delete(Websocket::getUserId() , $request['room']);
        }
        Websocket::join($request['room']);
        Room::add(Websocket::getUserId() , $request['room']);
        var_dump(Room::getRooms(Websocket::getUserId()));
    }

}
