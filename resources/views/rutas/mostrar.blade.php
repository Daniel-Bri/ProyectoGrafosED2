@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Ruta Calculada</h1>
</div>



@if(isset($ruta) && $ruta)
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="card-title mb-0">
            Ruta desde {{ $origenLugar->nombre }} hasta {{ $destinoLugar->nombre }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Información de la Ruta</h6>
                <p><strong>Distancia total:</strong> 
                    <span class="badge bg-primary">{{ $ruta['distancia_total'] ?? 'N/A' }} metros</span>
                </p>
                <p><strong>Tiempo estimado:</strong> 
                    <span class="badge bg-info">
                        {{ ceil(($ruta['distancia_total'] ?? 0) / 1.4 / 60) }} minutos (caminando)
                    </span>
                </p>
                
                <h6 class="mt-4">Camino a Seguir:</h6>
                <div class="list-group">
                    @foreach($ruta['camino'] ?? [] as $index => $paso)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Paso {{ $index + 1 }}</h6>
                                @if($index == 0)
                                    <span class="badge bg-success">Inicio</span>
                                @elseif($loop->last)
                                    <span class="badge bg-danger">Destino</span>
                                @else
                                    <span class="badge bg-secondary">Punto</span>
                                @endif
                            </div>
                            <p class="mb-1">{{ $paso }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('rutas.calcular') }}" class="btn btn-primary">Calcular Nueva Ruta</a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al Dashboard</a>
    </div>
</div>
@else
<div class="alert alert-warning">
    <h4>No se pudo calcular la ruta</h4>
    <p>No hay una ruta disponible entre los lugares seleccionados.</p>
    <a href="{{ route('rutas.calcular') }}" class="btn btn-primary">Intentar con otros lugares</a>
</div>
@endif
@endsection