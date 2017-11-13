@extends('layouts.app-panel')

@section('title')

  Create a New Message

@endsection

@section('content')

  <form class="form-horizontal" method="post" action="/messages">

    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="form-group">
      <label for="recipients" class="col-sm-2 control-label">Recipients</label>
      <div class="col-sm-10">
        <select multiple name="recipients[]" class="form-control">

@foreach ($recipients as $recipient)          
          <option value={{ $recipient->id }}>{{ $recipient->name }}</option>
@endforeach

        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="subject" class="col-sm-2 control-label">Subject</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject">
      </div>
    </div>

    <hr />

    <div class="form-group">
      <div class="col-sm-12">
        <textarea class="form-control" rows="10" name="body" id="body" placeholder="Blah blah blah..."></textarea>
      </div>
    </div>


    <div class="form-group">
      <div class="col-sm-12 text-center">
        <button type="submit" name="button" value="save" class="btn btn-default">Save</button>
        <button type="submit" name="button" value="send" class="btn btn-default">Send</button>
      </div>
    </div>

  </form>

@endsection