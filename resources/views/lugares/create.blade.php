@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Crear Nuevo Lugar</h1>
</div>

<form action="{{ route('lugares.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>
    
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="x" class="form-label">Coordenada X</label>
                <input type="number" class="form-control" id="x" name="x" step="0.1" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="y" class="form-label">Coordenada Y</label>
                <input type="number" class="form-control" id="y" name="y" step="0.1" required>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="categoria_id" class="form-label">Categoría</label>
        <select class="form-control" id="categoria_id" name="categoria_id" required>
            <option value="">Seleccione una categoría</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
            @endforeach
        </select>
    </div>
    
    <button type="submit" class="btn btn-success">Crear Lugar</button>
    <a href="{{ route('lugares.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection