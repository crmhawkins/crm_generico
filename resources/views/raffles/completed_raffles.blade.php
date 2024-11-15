@extends('layouts.app')

@section('titulo', 'Sorteos Finalizados')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-sm-12 col-md-4 order-md-1 order-last">
                <h3><i class="bi bi-gift"></i> Sorteos Finalizados</h3>
                <p class="text-subtitle text-muted">Listado de sorteos que ya han sido finalizados</p>
            </div>
            <div class="col-sm-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('raffles.index') }}">Sorteos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Finalizados</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <section class="section pt-4">
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre del Sorteo</th>
                            <th>Año</th>
                            <th>Fecha de Finalización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($completedRaffles as $raffle)
                            <tr>
                                <td>{{ $raffle->raffle_name }}</td>
                                <td>{{ $raffle->year }}</td>
                                <td>{{ $raffle->end_date ? \Carbon\Carbon::parse($raffle->end_date)->format('d/m/Y') : 'No definida' }}</td>
                                <td>
                                    <a href="{{ route('raffles.show_tickets', $raffle->id) }}" class="btn btn-info">Ver Tickets</a>
                                    
                                    @if ($raffle->winner_ticket_id)
                                        <a href="{{ route('raffles.show_winner', $raffle->id) }}" class="btn btn-success">Ver Ganador</a>
                                    @else
                                        <button class="btn btn-secondary" disabled>No hubo ganador</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<!-- Puedes añadir scripts adicionales aquí si los necesitas -->
@endsection
