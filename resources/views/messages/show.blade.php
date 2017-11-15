@extends('layouts.app-panel')

@section('title')

  <a href="{{ URL::previous() }}" class="btn btn-xs btn-default">Back</a>

  <div class="pull-right">


    <form class="button-form" method="post" action="/messages/{{ $message->id }}">
      {{ csrf_field() }}
      {{ method_field('DELETE') }}
      <button class="btn btn-xs btn-default">
        <i class="fa fa-trash" aria-hidden="true"></i>
      </button>
    </form>
@if ($show_star)
    <form class="button-form" method="post" action="/messages/{{ $message->id }}/star">
      {{ csrf_field() }}
      <button class="btn btn-xs btn-default"><strong>&#9734;</strong></button>
@endif
    </form>
  </div>


@endsection

@section('content')

  <form class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label">From</label>
    <div class="col-sm-10">
      <p class="form-control-static">{{ $message->sender()->first()->name }}</p>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">To</label>
    <div class="col-sm-10">
      <p class="form-control-static">
        
@foreach ($message->recipients()->get() as $recipient)

          {{ $recipient->name }}

@endforeach

      </p>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Subject</label>
    <div class="col-sm-10">
      <p class="form-control-static">{{ $message->subject }}</p>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Date</label>
    <div class="col-sm-10">
      <p class="form-control-static">{{ $message->prettySent() }}</p>
    </div>
  </div>
  <hr />
  <div class="form-group">
    <div class="col-sm-12">
      {{ $message->body }}
    </div>
  </div>
</form>

@endsection