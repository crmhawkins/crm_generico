@extends('layouts.app')

@section('titulo', 'Crear Sorteo')

@section('css')
<!-- Puedes añadir estilos adicionales aquí -->
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-gift"></i> Crear Sorteo</h3>
                    <p class="text-muted">Ingrese los datos del nuevo sorteo</p>
                </div>
                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('raffles.index') }}">Sorteos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear Sorteo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                    <form action="{{ route('raffles.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="raffle_name">Nombre del Sorteo</label>
                            <input type="text" name="raffle_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="year">Año</label>
                            <input type="number" name="year" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="ticket_digits">Dígitos del Ticket</label>
                            <input type="number" name="ticket_digits" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="start_date">Fecha de Inicio</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="end_date">Fecha de Fin</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
<!-- Puedes añadir scripts adicionales aquí -->
@endsection