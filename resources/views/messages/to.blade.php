@extends('layouts.app-panel')

@section('title')

  Inbox, Starred, or Deleted

@endsection

@section('content')

  <table class="table">
    <thead>
      <tr>
        <th>From</th>
        <th>Subject</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>

    @foreach ($messages as $message)
      <tr>
        <td>{{ $message->sender->name }}</td>
        <td>{{ $message->subject }}</td>
        <td>{{ $message->prettySent() }}</td>
      </tr>
    @endforeach

  </table>

@endsection