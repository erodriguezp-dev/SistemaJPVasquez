@extends('layouts.app')

@section('title', 'Editar Usuario - ' . config('app.name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold text-dark mb-0">Editar Usuario: {{ $usuario->usuario }}</h3>
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver a la lista
            </a>
        </div>

        @if ($esPropioUsuario)
            <div class="alert alert-warning small mb-4">
                <i class="bi bi-exclamation-triangle me-1"></i> Está editando su propio usuario de administrador. No puede modificar su propio rol ni desactivarse.
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('usuarios.update', $usuario) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold small text-secondary">Nombre Completo *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $usuario->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="usuario" class="form-label fw-bold small text-secondary">Nombre de Usuario *</label>
                            <input type="text" class="form-control @error('usuario') is-invalid @enderror" id="usuario" name="usuario" value="{{ old('usuario', $usuario->usuario) }}" required>
                            @error('usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold small text-secondary">Correo Electrónico (Opcional)</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $usuario->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="rol" class="form-label fw-bold small text-secondary">Rol asignado *</label>
                            @if ($esPropioUsuario)
                                <input type="hidden" name="rol" value="{{ $usuario->rol }}">
                                <select class="form-select bg-light" disabled>
                                    <option selected>{{ $roles[$usuario->rol] ?? $usuario->rol }} (Su rol actual)</option>
                                </select>
                            @else
                                <select class="form-select @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                                    @foreach ($roles as $key => $label)
                                        <option value="{{ $key }}" {{ old('rol', $usuario->rol) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @endif
                        </div>

                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-12"><p class="small text-muted mb-0 fw-bold">Cambiar Contraseña (dejar en blanco para mantener la actual):</p></div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold small text-secondary">Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Mínimo 8 caracteres">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password" title="Mostrar/ocultar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-bold small text-secondary">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation" title="Mostrar/ocultar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-arrow-repeat me-1"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
