@extends('layouts.app')

@section('content')
    <h1>{{ $raffle->raffle_name }} - Tickets</h1>

    <p>Start Date: {{ $raffle->start_date }}</p>
    <p>End Date: {{ $raffle->end_date }}</p>

    <h2>Select Winner</h2>
    <form method="POST" action="{{ route('raffles.setWinner', $raffle->id) }}">
        @csrf
        <label for="ticket_id">Ticket ID:</label>
        <select name="ticket_id" id="ticket_id">
            @foreach ($raffle->tickets as $ticket)
                <option value="{{ $ticket->id }}">{{ $ticket->ticket_number }} - Client: {{ $ticket->client->name }}</option>
            @endforeach
        </select>
        <button type="submit">Set Winner</button>
    </form>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif
@endsection
