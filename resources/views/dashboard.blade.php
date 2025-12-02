@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard - Mapa UAGRM</h1>
</div>

<!-- Mapa Grande -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-map me-2"></i>Mapa Interactivo de la Universidad
            <small class="text-muted">- Haz clic en el mapa para seleccionar coordenadas</small>
        </h5>
    </div>
    <div class="card-body p-0">
        <!-- Contenedor del mapa simplificado -->
        <div class="map-container-simple">
            <!-- Área del mapa -->
            <div class="map-area position-relative">
                <!-- Imagen del mapa -->
                <img src="{{ asset('images/uagrm.jpg') }}" alt="Mapa UAGRM" class="map-image" id="mapaImagen">
                
                <!-- Aquí se dibujarán los puntos -->
                <div id="puntosMapa"></div>
            </div>
            
            <!-- Coordenadas actuales y seleccionadas -->
            <div class="coordinates-panel p-3 bg-light border-top">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <span class="me-2"><strong>Coordenadas:</strong></span>
                            <span id="coordenadasSeleccionadas" class="text-muted">Ninguna seleccionada</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <small class="text-muted">
                                <span class="badge bg-danger me-2">●</span> {{ $totalLugares }} lugares
                                <span class="badge bg-primary ms-2 me-2">●</span> Nuevo
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary btn-sm" onclick="activarSeleccion()">
                            <i class="fas fa-crosshairs me-1"></i>Seleccionar Coordenadas
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarSeleccion()">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario y Lista de Lugares -->
<div class="row">
    <!-- Formulario para Agregar Lugar -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Lugar
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lugares.store') }}" method="POST" id="formLugar">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre del Lugar *</label>
                                <input type="text" class="form-control" name="nombre" placeholder="Ej: Biblioteca Central" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Categoría *</label>
                                <select class="form-control" name="categoria_id" required>
                                    <option value="">Selecciona categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Coordenada X *</label>
                                <input type="number" class="form-control" name="x" id="inputX" 
                                       placeholder="Haz clic en el mapa" required
                                       min="0" max="1000">
                                <small class="form-text text-muted">Coordenada horizontal</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Coordenada Y *</label>
                                <input type="number" class="form-control" name="y" id="inputY" 
                                       placeholder="Haz clic en el mapa" required
                                       min="0" max="1000">
                                <small class="form-text text-muted">Coordenada vertical</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción (opcional)</label>
                        <textarea class="form-control" name="descripcion" rows="2" placeholder="Descripción del lugar"></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Instrucciones:</strong> Haz clic en "Seleccionar Coordenadas" y luego haz clic en el mapa donde quieras colocar el lugar.
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" id="btnGuardar">
                        <i class="fas fa-save me-2"></i>Guardar Lugar en el Mapa
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Lista de Lugares -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Lugares Registrados
                    <span class="badge bg-secondary ms-2">{{ $lugares->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div style="max-height: 350px; overflow-y: auto;">
                    @if($lugares->count() > 0)
                        @foreach($lugares as $lugar)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong class="d-block">{{ $lugar->nombre }}</strong>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-location-dot me-1"></i>({{ $lugar->x }}, {{ $lugar->y }})
                                    </small>
                                    <span class="badge bg-info mt-1">{{ $lugar->categoria_nombre }}</span>
                                    @if($lugar->descripcion)
                                        <small class="d-block text-muted mt-1">
                                            {{ Str::limit($lugar->descripcion, 50) }}
                                        </small>
                                    @endif
                                </div>
                                <div class="btn-group btn-group-sm ms-2">
                                    <a href="{{ route('lugares.show', $lugar->id) }}" class="btn btn-outline-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-warning" 
                                            onclick="resaltarLugar({{ $lugar->x }}, {{ $lugar->y }}, '{{ $lugar->nombre }}')"
                                            title="Mostrar en mapa">
                                        <i class="fas fa-location-arrow"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                            <h5>No hay lugares registrados</h5>
                            <p class="mb-0">Comienza agregando el primer lugar en el mapa</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ESTILOS SIMPLIFICADOS */
.map-container-simple {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.map-area {
    min-height: 500px;
    overflow: auto;
    background: white;
}

.map-image {
    display: block;
    max-width: 100%;
    height: auto;
    min-height: 500px;
    cursor: default;
}

.map-image.seleccion-activa {
    cursor: crosshair;
}

.coordinates-panel {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Puntos del mapa */
#puntosMapa {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.punto-lugar {
    position: absolute;
    width: 16px;
    height: 16px;
    background-color: #dc3545;
    border-radius: 50%;
    border: 3px solid white;
    transform: translate(-50%, -50%);
    cursor: pointer;
    pointer-events: auto;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
}

.punto-lugar:hover {
    transform: translate(-50%, -50%) scale(1.3);
    z-index: 100;
    background-color: #c82333;
}

.punto-temporal {
    background-color: #007bff !important;
    width: 20px !important;
    height: 20px !important;
    z-index: 50;
    animation: pulse 1.5s infinite;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #007bff;
}

.punto-resaltado {
    background-color: #28a745 !important;
    width: 24px !important;
    height: 24px !important;
    z-index: 150;
    animation: pulse 2s infinite;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #28a745;
}

/* Animación para puntos */
@keyframes pulse {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
    100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
}

/* Mejoras para tarjetas */
.card {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 1rem;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
    padding: 1rem 1.25rem;
}

/* Estados del formulario */
.form-control:read-only {
    background-color: #f8f9fa;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
@endsection

@section('scripts')
<script>
let seleccionActiva = false;
let puntoTemporal = null;
let puntoResaltado = null;
let coordenadasActuales = null;

// Cuando la página carga, dibujamos los puntos en el mapa
document.addEventListener('DOMContentLoaded', function() {
    dibujarPuntosExistentes();
});

function obtenerCoordenadasReales(event) {
    const mapa = document.getElementById('mapaImagen');
    const rect = mapa.getBoundingClientRect();
    
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    
    return { x: Math.round(x), y: Math.round(y) };
}

function dibujarPuntosExistentes() {
    const puntosMapa = document.getElementById('puntosMapa');
    const lugares = @json($lugares);
    
    // Limpiar puntos existentes
    puntosMapa.innerHTML = '';
    
    // Dibujar cada lugar como un punto rojo
    lugares.forEach(lugar => {
        const punto = document.createElement('div');
        punto.className = 'punto-lugar';
        punto.style.left = lugar.x + 'px';
        punto.style.top = lugar.y + 'px';
        punto.title = `${lugar.nombre}\nCoordenadas: (${lugar.x}, ${lugar.y})\nCategoría: ${lugar.categoria_nombre}`;
        punto.setAttribute('data-lugar-id', lugar.id);
        
        // Hacer clic en un punto existente también selecciona sus coordenadas
        punto.addEventListener('click', function(e) {
            e.stopPropagation();
            seleccionarCoordenadas(lugar.x, lugar.y, lugar.nombre);
        });
        
        puntosMapa.appendChild(punto);
    });
}

function activarSeleccion() {
    seleccionActiva = true;
    const mapa = document.getElementById('mapaImagen');
    
    // Cambiar cursor y estilo
    mapa.classList.add('seleccion-activa');
    mapa.style.cursor = 'crosshair';
    
    // Remover puntos temporales
    if (puntoTemporal) {
        puntoTemporal.remove();
        puntoTemporal = null;
    }
    if (puntoResaltado) {
        puntoResaltado.remove();
        puntoResaltado = null;
    }
    
    // Actualizar panel
    document.getElementById('coordenadasSeleccionadas').textContent = 'Haz clic en el mapa...';
    document.getElementById('coordenadasSeleccionadas').className = 'text-warning';
    
    // Agregar evento de clic al mapa
    mapa.addEventListener('click', manejarClicMapa);
    
    // Mostrar mensaje
    mostrarMensaje('¡Modo selección activado! Haz clic en cualquier parte del mapa para seleccionar coordenadas.', 'info');
}

function manejarClicMapa(event) {
    if (!seleccionActiva) return;
    
    const coords = obtenerCoordenadasReales(event);
    seleccionarCoordenadas(coords.x, coords.y);
}

function seleccionarCoordenadas(x, y, nombreLugar = 'Nueva ubicación') {
    // Guardar coordenadas
    coordenadasActuales = { x: x, y: y };
    
    // Actualizar inputs del formulario (AHORA SÍ EDITABLES)
    document.getElementById('inputX').value = x;
    document.getElementById('inputY').value = y;
    
    // Quitar atributo readonly para permitir edición manual
    document.getElementById('inputX').readOnly = false;
    document.getElementById('inputY').readOnly = false;
    
    // Actualizar panel de coordenadas
    const coordenadasText = `(${x}, ${y}) - ${nombreLugar}`;
    document.getElementById('coordenadasSeleccionadas').textContent = coordenadasText;
    document.getElementById('coordenadasSeleccionadas').className = 'text-success fw-bold';
    
    // Mostrar punto temporal azul
    mostrarPuntoTemporal(x, y);
    
    // Desactivar selección
    desactivarSeleccion();
    
    // Mostrar mensaje de éxito
    mostrarMensaje(`Coordenadas seleccionadas: (${x}, ${y})`, 'success');
}

function desactivarSeleccion() {
    seleccionActiva = false;
    const mapa = document.getElementById('mapaImagen');
    
    // Restaurar cursor y estilo
    mapa.classList.remove('seleccion-activa');
    mapa.style.cursor = 'default';
    mapa.removeEventListener('click', manejarClicMapa);
}

function mostrarPuntoTemporal(x, y) {
    // Remover punto temporal anterior
    if (puntoTemporal) {
        puntoTemporal.remove();
    }
    
    // Crear nuevo punto temporal azul
    puntoTemporal = document.createElement('div');
    puntoTemporal.className = 'punto-lugar punto-temporal';
    puntoTemporal.style.left = x + 'px';
    puntoTemporal.style.top = y + 'px';
    puntoTemporal.title = 'Ubicación seleccionada - Lista para guardar';
    
    document.getElementById('puntosMapa').appendChild(puntoTemporal);
}

function resaltarLugar(x, y, nombre = 'Lugar') {
    // Remover punto resaltado anterior
    if (puntoResaltado) {
        puntoResaltado.remove();
    }
    
    // Crear punto resaltado verde
    puntoResaltado = document.createElement('div');
    puntoResaltado.className = 'punto-lugar punto-resaltado';
    puntoResaltado.style.left = x + 'px';
    puntoResaltado.style.top = y + 'px';
    puntoResaltado.title = `${nombre} - Coordenadas: (${x}, ${y})`;
    
    document.getElementById('puntosMapa').appendChild(puntoResaltado);
    
    // Seleccionar estas coordenadas también
    seleccionarCoordenadas(x, y, nombre);
    
    // Mostrar mensaje
    mostrarMensaje(`Lugar resaltado: ${nombre} en (${x}, ${y})`, 'info');
}

function limpiarSeleccion() {
    // Limpiar inputs
    document.getElementById('inputX').value = '';
    document.getElementById('inputY').value = '';
    
    // Restaurar readonly
    document.getElementById('inputX').readOnly = true;
    document.getElementById('inputY').readOnly = true;
    
    // Limpiar panel
    document.getElementById('coordenadasSeleccionadas').textContent = 'Ninguna seleccionada';
    document.getElementById('coordenadasSeleccionadas').className = 'text-muted';
    
    // Remover puntos temporales
    if (puntoTemporal) {
        puntoTemporal.remove();
        puntoTemporal = null;
    }
    if (puntoResaltado) {
        puntoResaltado.remove();
        puntoResaltado = null;
    }
    
    // Desactivar selección
    desactivarSeleccion();
    
    mostrarMensaje('Selección limpiada', 'warning');
}

function mostrarMensaje(mensaje, tipo = 'info') {
    // Crear toast simple
    const toast = document.createElement('div');
    toast.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-remover después de 4 segundos
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 4000);
}

// Validación del formulario
document.getElementById('formLugar').addEventListener('submit', function(e) {
    const x = document.getElementById('inputX').value;
    const y = document.getElementById('inputY').value;
    
    if (!x || !y) {
        e.preventDefault();
        mostrarMensaje('Por favor selecciona coordenadas en el mapa antes de guardar', 'danger');
        activarSeleccion();
    }
});
</script>
@endsection