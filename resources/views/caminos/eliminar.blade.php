@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Eliminar Camino</h1>
</div>

<div class="alert alert-danger">
    <h4>¿Estás seguro de que quieres eliminar este camino?</h4>
    <p><strong>Origen:</strong> {{ $camino->origen->nombre }}</p>
    <p><strong>Destino:</strong> {{ $camino->destino->nombre }}</p>
    <p><strong>Distancia:</strong> {{ $camino->distancia }} metros</p>
    <p><strong>Tipo:</strong> 
        @if($camino->es_bidireccional)
            Bidireccional
        @else
            Unidireccional
        @endif
    </p>
</div>

<form action="{{ route('caminos.destroy', $camino) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
    <a href="{{ route('caminos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection