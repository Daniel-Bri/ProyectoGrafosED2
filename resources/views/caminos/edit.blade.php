@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Camino</h1>
</div>

<form action="{{ route('caminos.update', $camino) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="lugar_origen_id" class="form-label">Lugar Origen</label>
                <select class="form-control" id="lugar_origen_id" name="lugar_origen_id" required>
                    <option value="">Seleccione lugar origen</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id }}" {{ $camino->lugar_origen_id == $lugar->id ? 'selected' : '' }}>
                            {{ $lugar->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="lugar_destino_id" class="form-label">Lugar Destino</label>
                <select class="form-control" id="lugar_destino_id" name="lugar_destino_id" required>
                    <option value="">Seleccione lugar destino</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id }}" {{ $camino->lugar_destino_id == $lugar->id ? 'selected' : '' }}>
                            {{ $lugar->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="distancia" class="form-label">Distancia (metros)</label>
        <input type="number" class="form-control" id="distancia" name="distancia" step="0.1" min="0" value="{{ $camino->distancia }}" required>
    </div>
    
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="es_bidireccional" name="es_bidireccional" value="1" {{ $camino->es_bidireccional ? 'checked' : '' }}>
            <label class="form-check-label" for="es_bidireccional">
                Camino bidireccional
            </label>
        </div>
    </div>
    
    <button type="submit" class="btn btn-warning">Actualizar Camino</button>
    <a href="{{ route('caminos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection