@extends('layouts.app')

@section('titulo', 'Tickets del Sorteo')

@section('css')
    <link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        <!-- Encabezado de la página -->
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-ticket"></i> Tickets del Sorteo: {{ $raffle->raffle_name }}</h3>
                    <p class="text-subtitle text-muted">Resumen de tickets vendidos y disponibles</p>
                </div>
                <div class="col-sm-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('raffles.index') }}">Sorteos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tickets</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Notificación de éxito o error -->
        @if (session('success'))
            <div class="alert alert-success" style="position: fixed; bottom: 20px; right: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" style="position: fixed; bottom: 20px; right: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Resumen de tickets vendidos y Buscador -->
        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    <h5>Tickets vendidos: {{ $ticketsSold }} / {{ $totalTickets }}</h5>

                    <!-- Buscador de Tickets -->
                    <input type="text" id="ticketSearch" class="form-control mb-3" placeholder="Buscar por ID de Ticket o Número de Ticket...">

                    <!-- Tabla de Tickets -->
                    <table class="table" id="ticketsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Número de Ticket</th>
                                <th>ID del Cliente</th>
                                <th>Fecha de Compra</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->id }}</td>
                                    <td>{{ $ticket->ticket_number }}</td>
                                    <td>{{ $ticket->client_id ?? 'Sin asignar' }}</td>
                                    <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
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
    @include('partials.toast')

    <!-- Script para el buscador de tickets -->
    <script>
        document.getElementById('ticketSearch').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#ticketsTable tbody tr');
            
            rows.forEach(row => {
                const ticketID = row.cells[0].textContent.toLowerCase();
                const ticketNumber = row.cells[1].textContent.toLowerCase();
                const clientID = row.cells[2].textContent.toLowerCase();

                if (ticketID.includes(query) || ticketNumber.includes(query) || clientID.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection

