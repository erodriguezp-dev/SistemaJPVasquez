@extends('layouts.app')

@section('title', '403 Acceso Denegado - ' . config('app.name'))

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-8 col-lg-6 text-center">
        <div class="card border-0 shadow-sm p-4 p-md-5 rounded-3">
            <div class="mb-3 text-warning">
                <i class="bi bi-shield-lock-fill" style="font-size: 4rem;"></i>
            </div>
            <h1 class="display-5 fw-bold text-dark mb-2">403</h1>
            <h4 class="fw-bold text-secondary mb-3">Acceso Denegado</h4>
            <p class="text-muted mb-4">
                No tiene los permisos requeridos para ingresar a este módulo o recurso del sistema.
            </p>
            <div>
                <a href="{{ url('/') }}" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-house-door me-1"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
