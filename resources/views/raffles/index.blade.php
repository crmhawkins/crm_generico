@extends('layouts.app')

@section('titulo', 'Sorteos')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        {{-- Títulos --}}
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
            <!-- Botón para Añadir Sorteo -->
            <div class="mt-3">
                <a href="{{ route('raffles.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus mx-auto"></i> Añadir Sorteo</a>
            </div>
        </div>

        <!-- Mensajes de notificación -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

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
                                        <!-- Botón para Ver -->
                                        <a href="{{ route('raffles.show', $raffle->id) }}" class="btn btn-info">Ver</a>

                                        <!-- Botón para Editar -->
                                        <a href="{{ route('raffles.edit', $raffle->id) }}" class="btn btn-warning">Editar</a>

                                        <!-- Botón para Eliminar -->
                                        <form action="{{ route('raffles.destroy', $raffle->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>

                                        <!-- Formulario para Asignar Ganador -->
                                        <form action="{{ route('raffles.assign_winner', $raffle->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="text" name="ticket_number" placeholder="Nº Ticket Ganador" required class="form-control" style="width: auto; display: inline;">
                                            <button type="submit" class="btn btn-primary">Asignar Ganador</button>
                                        </form>
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
    @include('partials.toast')
@endsection

