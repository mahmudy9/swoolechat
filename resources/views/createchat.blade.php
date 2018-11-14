@extends('layouts.app')

@section('content')
<form action="{{url('/store-chat')}}" method="post">
    @csrf
    <div class="form-group">
      <label for="exampleFormControlSelect1">Select User</label>
      <select class="form-control" name="user2">
        @foreach($users as $user)
        <option value="{{$user->id}}" >{{$user->name}}</option>
        @endforeach
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Create Chat</button>
    </div>
</form>
@endsection
