@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Eliminar Lugar</h1>
</div>

<div class="alert alert-danger">
    <h4>¿Estás seguro de que quieres eliminar este lugar?</h4>
    <p><strong>Nombre:</strong> {{ $lugar->nombre }}</p>
    <p><strong>Descripción:</strong> {{ $lugar->descripcion ?? 'N/A' }}</p>
    <p><strong>Categoría:</strong> {{ $lugar->categoria_nombre ?? 'N/A' }}</p>
    
    <?php
        $caminosOrigen = DB::table('caminos')->where('lugar_origen_id', $lugar->id)->count();
        $caminosDestino = DB::table('caminos')->where('lugar_destino_id', $lugar->id)->count();
        $totalCaminos = $caminosOrigen + $caminosDestino;
    ?>
    
    @if($totalCaminos > 0)
        <div class="alert alert-warning mt-3">
            <strong>¡Advertencia!</strong> Este lugar tiene {{ $totalCaminos }} caminos conectados. 
            Al eliminar el lugar, también se eliminarán todos sus caminos.
        </div>
    @endif
</div>

<form action="{{ route('lugares.destroy', $lugar->id) }}" method="POST"> {{-- CAMBIADO: usar $lugar->id --}}
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
    <a href="{{ route('lugares.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection