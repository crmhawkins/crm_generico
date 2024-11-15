@extends('layouts.app')

@section('titulo', 'Editar Sorteo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        {{-- Títulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-gift"></i> Editar Sorteo</h3>
                    <p class="text-subtitle text-muted">Modifica los detalles del sorteo</p>
                </div>
                <div class="col-sm-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('raffles.index') }}">Sorteos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar Sorteo</li>
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

                    <form action="{{ route('raffles.updateRaffle', $raffle->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="raffle_name">Nombre del Sorteo</label>
                            <input type="text" name="raffle_name" class="form-control" value="{{ $raffle->raffle_name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="year">Año</label>
                            <input type="number" name="year" class="form-control" value="{{ $raffle->year }}" required>
                        </div>

                        <div class="form-group">
                            <label for="ticket_digits">Dígitos del Ticket</label>
                            <input type="number" name="ticket_digits" class="form-control" value="{{ $raffle->ticket_digits }}" required>
                        </div>

                        <div class="form-group">
                            <label for="start_date">Fecha de Inicio</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="{{ $raffle->start_date ? \Carbon\Carbon::parse($raffle->start_date)->format('Y-m-d') : '' }}">
                        </div>

                        <div class="form-group">
                            <label for="end_date">Fecha de Finalización</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ $raffle->end_date ? \Carbon\Carbon::parse($raffle->end_date)->format('Y-m-d') : '' }}">
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    @include('partials.toast')
@endsection
