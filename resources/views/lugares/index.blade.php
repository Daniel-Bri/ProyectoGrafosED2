@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Lugares</h1>
    <a href="{{ route('lugares.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Lugar
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>¡Éxito!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Lugares
            <span class="badge bg-secondary ms-2">{{ count($lugares) }}</span>
        </h5>
    </div>
    <div class="card-body">
        @if($lugares->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Coordenadas</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lugares as $lugar)
                    <tr>
                        <td><strong>#{{ $lugar->id }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                <strong>{{ $lugar->nombre }}</strong>
                            </div>
                        </td>
                        <td>
                            @if($lugar->descripcion)
                                <span class="text-truncate" style="max-width: 200px;" title="{{ $lugar->descripcion }}">
                                    {{ Str::limit($lugar->descripcion, 50) }}
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                ({{ number_format($lugar->x, 1) }}, {{ number_format($lugar->y, 1) }})
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $lugar->categoria_nombre }}
                            </span>
                        </td>
                        <td>
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('lugares.show', $lugar->id) }}" 
           class="btn btn-info" 
           title="Ver detalles">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('lugares.edit', $lugar->id) }}" 
           class="btn btn-warning" 
           title="Editar">
            <i class="fas fa-edit"></i>
        </a>
        <a href="{{ route('lugares.eliminar', $lugar->id) }}"  {{-- Asegurar que es $lugar->id --}}
           class="btn btn-danger" 
           title="Eliminar">
            <i class="fas fa-trash"></i>
        </a>
    </div>
</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-map-marker-alt fa-4x text-muted"></i>
            </div>
            <h4 class="text-muted">No hay lugares registrados</h4>
            <p class="text-muted">Comienza agregando el primer lugar a tu mapa universitario.</p>
            <a href="{{ route('lugares.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-2"></i>Agregar Primer Lugar
            </a>
        </div>
        @endif
    </div>
    @if($lugares->count() > 0)
    <div class="card-footer">
        <div class="row">
            <div class="col-md-6">
                <small class="text-muted">
                    Mostrando <strong>{{ $lugares->count() }}</strong> lugares registrados
                </small>
            </div>
            <div class="col-md-6 text-end">
                <small class="text-muted">
                    Última actualización: {{ now()->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Estadísticas rápidas -->
@if($lugares->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ $lugares->count() }}</h4>
                <p class="mb-0">Total Lugares</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $lugares->where('x', '>', 0)->where('y', '>', 0)->count() }}</h4>
                <p class="mb-0">Con Coordenadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $lugares->where('descripcion', '!=', '')->count() }}</h4>
                <p class="mb-0">Con Descripción</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h4>{{ $categorias_count ?? 'N/A' }}</h4>
                <p class="mb-0">Categorías</p>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.table th {
    border-top: none;
    font-weight: 600;
}
.btn-group .btn {
    border-radius: 0.375rem;
    margin: 0 2px;
}
.text-truncate {
    display: inline-block;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirmación antes de eliminar
    $('.btn-danger').on('click', function(e) {
        if (!confirm('¿Estás seguro de que quieres eliminar este lugar?')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection