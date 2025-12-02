@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalles del Lugar: {{ $lugar->nombre }}</h1>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Información General</h5>
                <p><strong>ID:</strong> {{ $lugar->id }}</p>
                <p><strong>Nombre:</strong> {{ $lugar->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $lugar->descripcion ?? 'N/A' }}</p>
                <p><strong>Coordenadas:</strong> ({{ $lugar->x }}, {{ $lugar->y }})</p>
                <p><strong>Categoría:</strong> {{ $lugar->categoria_nombre }}</p>
                <p><strong>Creado:</strong> 
                    @if($lugar->created_at)
                        {{ date('d/m/Y H:i', strtotime($lugar->created_at)) }}
                    @else
                        N/A
                    @endif
                </p>
                <p><strong>Actualizado:</strong> 
                    @if($lugar->updated_at)
                        {{ date('d/m/Y H:i', strtotime($lugar->updated_at)) }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <h5>Caminos Conectados</h5>
                @if($caminosOrigen->count() > 0 || $caminosDestino->count() > 0)
                    <ul class="list-group">
                        @foreach($caminosOrigen as $camino)
                            <li class="list-group-item">
                                → Hacia: {{ $camino->destino_nombre }} 
                                ({{ $camino->distancia }}m)
                                @if($camino->es_bidireccional)
                                    <span class="badge bg-success">Bidireccional</span>
                                @endif
                            </li>
                        @endforeach
                        @foreach($caminosDestino as $camino)
                            <li class="list-group-item">
                                ← Desde: {{ $camino->origen_nombre }} 
                                ({{ $camino->distancia }}m)
                                @if($camino->es_bidireccional)
                                    <span class="badge bg-success">Bidireccional</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No hay caminos conectados a este lugar.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('lugares.edit', $lugar->id) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('lugares.eliminar', $lugar->id) }}" class="btn btn-danger">Eliminar</a>
        <a href="{{ route('lugares.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection