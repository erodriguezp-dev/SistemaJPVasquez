@extends('layouts.app')

@section('title', $nombreModulo . ' - ' . config('app.name'))

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-8 text-center">
        <div class="card border-0 shadow-sm p-4 p-md-5 rounded-3">
            <div class="mb-3 text-primary">
                <i class="bi bi-cone-striped" style="font-size: 3.5rem;"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">{{ $nombreModulo }}</h3>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill mb-3 d-inline-block">
                <i class="bi bi-tools me-1"></i> Módulo en construcción
            </span>
            <p class="text-secondary mb-4">
                Esta funcionalidad se encuentra planificada y lista para ser implementada en las siguientes fases del desarrollo.
            </p>
            <div>
                <a href="{{ url('/') }}" class="btn btn-outline-primary px-4 fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Regresar a la página principal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
