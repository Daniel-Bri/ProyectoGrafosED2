@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Crear Nuevo Camino</h1>
</div>

<form action="{{ route('caminos.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="lugar_origen_id" class="form-label">Lugar Origen</label>
                <select class="form-control" id="lugar_origen_id" name="lugar_origen_id" required>
                    <option value="">Seleccione lugar origen</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id }}">{{ $lugar->nombre }}</option>
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
                        <option value="{{ $lugar->id }}">{{ $lugar->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="distancia" class="form-label">Distancia (metros)</label>
        <input type="number" class="form-control" id="distancia" name="distancia" step="0.1" min="0" required>
    </div>
    
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="es_bidireccional" name="es_bidireccional" value="1" checked>
            <label class="form-check-label" for="es_bidireccional">
                Camino bidireccional
            </label>
        </div>
        <small class="form-text text-muted">Si está marcado, el camino funcionará en ambas direcciones.</small>
    </div>
    
    <button type="submit" class="btn btn-success">Crear Camino</button>
    <a href="{{ route('caminos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const origenSelect = document.getElementById('lugar_origen_id');
    const destinoSelect = document.getElementById('lugar_destino_id');
    
    origenSelect.addEventListener('change', function() {
        // Deshabilitar la opción seleccionada en origen en el destino
        Array.from(destinoSelect.options).forEach(option => {
            option.disabled = option.value === this.value;
        });
    });
});
</script>
@endsection