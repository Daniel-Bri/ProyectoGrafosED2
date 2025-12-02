@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Calcular Ruta</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Seleccionar Origen y Destino</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('rutas.calcular-ruta') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="origen_id" class="form-label">Lugar Origen</label>
                        <select class="form-control" id="origen_id" name="origen_id" required>
                            <option value="">Seleccione lugar origen</option>
                            @foreach($lugares as $lugar)
                                <option value="{{ $lugar->id }}">{{ $lugar->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="destino_id" class="form-label">Lugar Destino</label>
                        <select class="form-control" id="destino_id" name="destino_id" required>
                            <option value="">Seleccione lugar destino</option>
                            @foreach($lugares as $lugar)
                                <option value="{{ $lugar->id }}">{{ $lugar->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-route me-2"></i>Calcular Ruta Óptima
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Información</h5>
            </div>
            <div class="card-body">
                <p>Este sistema calcula la ruta más corta entre dos puntos usando el algoritmo de Dijkstra.</p>
                <p><strong>Características:</strong></p>
                <ul>
                    <li>Considera las distancias entre lugares</li>
                    <li>Toma en cuenta caminos bidireccionales y unidireccionales</li>
                    <li>Calcula la ruta óptima en base a la distancia total</li>
                    <li>Muestra el camino paso a paso</li>
                </ul>
            </div>
        </div>
        
        @if(session('ruta'))
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Ruta Calculada</h5>
                </div>
                <div class="card-body">
                    @php $ruta = session('ruta'); @endphp
                    <p><strong>Distancia total:</strong> {{ $ruta['distancia_total'] ?? 'N/A' }} metros</p>
                    <p><strong>Camino:</strong></p>
                    <ol>
                        @foreach($ruta['camino'] ?? [] as $paso)
                            <li>{{ $paso }}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection