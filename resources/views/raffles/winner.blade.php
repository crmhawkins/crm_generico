@extends('layouts.app')

@section('titulo', 'Ganador del Sorteo')

@section('css')
<!-- Aquí puedes añadir estilos adicionales si es necesario -->
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-sm-12 col-md-4 order-md-1 order-last">
                <h3><i class="bi bi-gift"></i> Ganador del Sorteo</h3>
                <p class="text-subtitle text-muted">Detalles del ganador para el sorteo {{ $raffle->raffle_name }}</p>
            </div>
            <div class="col-sm-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('raffles.index') }}">Sorteos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ganador</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="card">
            <div class="card-body">
                <h4>Sorteo: {{ $raffle->raffle_name }}</h4>
                @if ($ticket)
                <p><strong>Número de Ticket Ganador:</strong> {{ $ticket->ticket_number }}</p>
                <p><strong>Nombre del Cliente:</strong> {{ $client->name }}</p>
                <p><strong>Email del Cliente:</strong> {{ $client->email }}</p>
            @else
                <div class="alert alert-danger">
                    <strong>No se encontró un ganador válido.</strong>
                    <p>El número asignado no corresponde a ningún ticket vendido para este sorteo.</p>
                </div>
            @endif
                <a href="{{ route('raffles.index') }}" class="btn btn-secondary mt-3">Volver a Sorteos</a>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<!-- Puedes añadir scripts adicionales aquí si los necesitas -->
@endsection
