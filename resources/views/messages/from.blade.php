@extends('layouts.app-panel')

@section('title')

  {{ $title }}

@endsection

@section('content')

    <table class="table">
    <thead>
      <tr>
        <th></th>
        <th>To</th>
        <th>Subject</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>

    @foreach ($messages as $message)
      <tr onclick="document.location='{{ $message->link }}'">
        <td></td>
        <td>
          <ul class="list-unstyled">
          @foreach ($message->recipients as $recipient)
            <li>{{ $recipient->name }}</li>
          @endforeach
          </ul>
        </td>
        <td>{{ $message->subject }}</td>
        <td>{{ $message->prettyUpdated() }}</td>
      </tr>
    @endforeach

  </table>

@endsection