@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Caminos</h1>
    <a href="{{ route('caminos.create') }}" class="btn btn-primary">Nuevo Camino</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Distancia (m)</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caminos as $camino)
            <tr>
                <td>{{ $camino->id }}</td>
                <td>{{ $camino->origen->nombre }}</td>
                <td>{{ $camino->destino->nombre }}</td>
                <td>{{ $camino->distancia }}</td>
                <td>
                    @if($camino->es_bidireccional)
                        <span class="badge bg-success">Bidireccional</span>
                    @else
                        <span class="badge bg-warning">Unidireccional</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('caminos.show', $camino) }}" class="btn btn-sm btn-info">Ver</a>
                    <a href="{{ route('caminos.edit', $camino) }}" class="btn btn-sm btn-warning">Editar</a>
                    <a href="{{ route('caminos.eliminar', $camino) }}" class="btn btn-sm btn-danger">Eliminar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection