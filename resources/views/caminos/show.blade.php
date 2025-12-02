@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalles del Camino</h1>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Información del Camino</h5>
                <p><strong>ID:</strong> {{ $camino->id }}</p>
                <p><strong>Origen:</strong> {{ $camino->origen->nombre }}</p>
                <p><strong>Destino:</strong> {{ $camino->destino->nombre }}</p>
                <p><strong>Distancia:</strong> {{ $camino->distancia }} metros</p>
                <p><strong>Tipo:</strong> 
                    @if($camino->es_bidireccional)
                        <span class="badge bg-success">Bidireccional</span>
                    @else
                        <span class="badge bg-warning">Unidireccional</span>
                    @endif
                </p>
                <p><strong>Creado:</strong> {{ $camino->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Actualizado:</strong> {{ $camino->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="col-md-6">
                <h5>Información de Lugares</h5>
                <div class="card">
                    <div class="card-body">
                        <h6>Lugar Origen</h6>
                        <p><strong>Nombre:</strong> {{ $camino->origen->nombre }}</p>
                        <p><strong>Coordenadas:</strong> ({{ $camino->origen->x }}, {{ $camino->origen->y }})</p>
                        <p><strong>Categoría:</strong> {{ $camino->origen->categoria->nombre }}</p>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-body">
                        <h6>Lugar Destino</h6>
                        <p><strong>Nombre:</strong> {{ $camino->destino->nombre }}</p>
                        <p><strong>Coordenadas:</strong> ({{ $camino->destino->x }}, {{ $camino->destino->y }})</p>
                        <p><strong>Categoría:</strong> {{ $camino->destino->categoria->nombre }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('caminos.edit', $camino) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('caminos.eliminar', $camino) }}" class="btn btn-danger">Eliminar</a>
        <a href="{{ route('caminos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection