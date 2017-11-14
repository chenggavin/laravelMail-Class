@extends('layouts.app-panel')

@section('title')

  {{ $title }}

  <a href="/messages/create" class="pull-right btn btn-xs btn-default">New</a>

@endsection

@section('content')

  <table class="table">
    <thead>
      <tr>
        <th></th>
        <th>From</th>
        <th>Subject</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>

    @foreach ($messages as $message)
      <tr onclick="document.location='/messages/{{ $message->id }}'" class="{{ $message->pivot->is_read == true ? '' : 'unread' }}">
        <td>
          @if ($message->is_starred)
            <strong>&#9734;</strong>
          @endif
        </td>
        <td>{{ $message->sender->name }}</td>
        <td>{{ $message->subject }}</td>
        <td>{{ $message->prettySent() }}</td>
      </tr>
    @endforeach

  </table>

@endsection