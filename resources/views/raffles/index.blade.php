@extends('layouts.app')

@section('titulo', 'Sorteos')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-sm-12 col-md-4 order-md-1 order-last">
                <h3><i class="bi bi-gift"></i> Sorteos</h3>
                <p class="text-subtitle text-muted">Gestión y creación de sorteos</p>
            </div>
            <div class="col-sm-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sorteos</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('raffles.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus mx-auto"></i> Añadir Sorteo</a>
            <a href="{{ route('raffles.show_completed') }}" class="btn btn-secondary"><i class="fa-solid fa-archive mx-auto"></i> Ver Finalizados</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Formulario para asignar tickets a un cliente -->
    <div class="card mt-4">
        <div class="card-body">
            <h5>Asignar Tickets a Cliente</h5>
            <form id="assignTicketsForm">
                <div class="mb-3">
                    <label for="raffle_id" class="form-label">ID del Sorteo:</label>
                    <input type="number" class="form-control" id="raffle_id" name="raffle_id" required>
                </div>
                <div class="mb-3">
                    <label for="client_id" class="form-label">ID del Cliente:</label>
                    <input type="number" class="form-control" id="client_id" name="client_id" required>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Cantidad de Tickets:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                </div>
                <button type="button" onclick="assignTickets()" class="btn btn-primary">Asignar Tickets</button>
            </form>
            <div id="result" class="mt-3"></div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre del Sorteo</th>
                            <th>Año</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($raffles as $raffle)
                            <tr>
                                <td>{{ $raffle->raffle_name }}</td>
                                <td>{{ $raffle->year }}</td>
                                <td>
                                    <a href="{{ route('raffles.show_tickets', $raffle->id) }}" class="btn btn-info">Ver Tickets</a>
                                    <a href="{{ route('raffles.edit', $raffle->id) }}" class="btn btn-warning" @if ($raffle->status === 1) disabled @endif>Editar</a>
                                    @if ($raffle->status === 0)
                                        <form action="{{ route('raffles.finalize', $raffle->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Estás seguro de que deseas finalizar este sorteo?')">Finalizar</button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary" disabled>Finalizado</button>
                                    @endif
                                    @if ($raffle->winner_ticket_id)
                                        <a href="{{ route('raffles.show_winner', $raffle->id) }}" class="btn btn-success">Ver Ganador</a>
                                    @else
                                        <form action="{{ route('raffles.assign_winner', $raffle->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="text" name="ticket_number" placeholder="Nº Ticket Ganador" required class="form-control" style="width: auto; display: inline;">
                                            <button type="submit" class="btn btn-primary">Asignar Ganador</button>
                                        </form>
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

<!-- Script para manejar la solicitud de asignación de tickets -->
<script>
    function assignTickets() {
        const raffleId = document.getElementById('raffle_id').value;
        const clientId = document.getElementById('client_id').value;
        const quantity = document.getElementById('quantity').value;

        fetch('http://localhost/crm_generico/public/api/assign-tickets', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                raffle_id: raffleId,
                client_id: clientId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('result').innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            } else {
                document.getElementById('result').innerHTML = `
                    <div class="alert alert-success">
                        <h5>Tickets Asignados</h5>
                        <p><strong>Sorteo:</strong> ${data.raffle_name} (${data.year})</p>
                        <p><strong>Tickets Asignados:</strong> ${data.assigned_tickets.join(', ')}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = `<div class="alert alert-danger">Error al asignar los tickets.</div>`;
        });
    }
</script>
@endsection

