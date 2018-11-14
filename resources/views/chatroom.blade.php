@extends('layouts.app')

@section('content')

@if(!$messages)

    <h3>No Messages in your chat with {{$to->name}} yet</h3>
@else
    <div id="msgs">
    @foreach($messages as $message)
        <p><em><b>{{$message->fromusername}}</b></em> : {{$message->body}}</p>
        <hr>
    @endforeach
    </div>
@endif

<form id="chatform" >
<div class="form-group">
<input type="text" id="body"/>
</div>

<div class="form-group">
    <input type="submit" class="btn btn-primary" value="Send" />
</div>
</form>

@endsection

@section('script')
<script>
    var room = 'room'+{{$chatid}};
    socket.emit('join_room' , {room:room});
    socket.removeAllListeners('reply');
    socket.on('reply' , function(msg){
        //console.log(msg);
        $('#msgs').append(`<p><em><b>${msg.fromusername}</b></em> : ${msg.body}</p><hr>`)
    });
    
    $('#chatform').submit(function(e) {
        e.preventDefault();
        var body = $('#body').val();
        var chatid ={{$chatid}} ;
        axios.post('/store-message' , {
            body:body,
            chatid:chatid
        }).then( function(response){
            //console.log(response);

            socket.removeAllListeners('receive_msg');
            socket.emit('send_msg' , {data:response , room:room});

            socket.on('receive_msg' , function(msg) {
                //console.log(msg);

                $('#msgs').append(`<p><em><b>${msg.fromusername}</b></em> : ${msg.body}</p><hr>`);
                $('#body').val('');
            });
            
        }).catch(function(error){
            socket.removeAllListeners('error_msg');

            socket.emit('validation_error' ,{data: error});
            socket.on('error_msg' , function(msg){

                console.log(msg);
            });
            //console.error(error);
        });
    })

</script>
@endsection
