@extends('layouts.app')

@section('titulo', 'Clasificación por Usuario')

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-12">
                <h3>Clasificación por Usuario</h3>
                <p class="text-subtitle text-muted">Resultados clasificados por usuario</p>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        @foreach ($clasificacion as $usuario => $cambios)
            <div class="card mb-3">
                <div class="card-body">
                    <h4>Usuario: {{ $usuarios[$usuario]->name ?? 'Usuario Desconocido' }}</h4>
                    @foreach ($cambios as $tipo => $detalles)
                        <h5 class="mt-3">{{ ucfirst($tipo) }}</h5>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>De</th>
                                    <th>A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detalles as $detalle)
                                    <tr>
                                        <td>{{ $detalle['antiguo'] }}</td>
                                        <td>{{ $detalle['nuevo'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>
</div>
@endsection
