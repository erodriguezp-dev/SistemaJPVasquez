<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Muestra la lista paginada de usuarios con filtro de búsqueda.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('buscar'));
        $rol = $request->input('rol');

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('usuario', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($rol && array_key_exists($rol, config('permissions.roles', []))) {
            $query->where('rol', $rol);
        }

        $usuarios = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('usuarios.index', compact('usuarios', 'search', 'rol'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create(): View
    {
        $roles = config('permissions.roles', []);
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Almacena un usuario en la base de datos.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['activo'] = true;

        User::create($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario.
     */
    public function edit(User $usuario): View
    {
        $roles = config('permissions.roles', []);
        $esPropioUsuario = auth()->id() === $usuario->id;

        return view('usuarios.edit', compact('usuario', 'roles', 'esPropioUsuario'));
    }

    /**
     * Actualiza la información del usuario en la base de datos.
     */
    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $validated = $request->validated();

        // Autoprotección: El administrador en sesión no puede cambiar su propio rol
        if (auth()->id() === $usuario->id && $validated['rol'] !== $usuario->rol) {
            return back()->withErrors([
                'rol' => 'No puede cambiar su propio rol de administrador.',
            ])->withInput();
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Activa o desactiva la cuenta de un usuario (Toggle Status).
     */
    public function toggleStatus(User $usuario): RedirectResponse
    {
        // Autoprotección: El administrador no puede desactivarse a sí mismo
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No puede desactivar su propia cuenta de administrador.');
        }

        $usuario->activo = ! $usuario->activo;
        $usuario->save();

        $estadoTexto = $usuario->activo ? 'activado' : 'desactivado';

        return redirect()->route('usuarios.index')->with('success', "El usuario {$usuario->usuario} ha sido {$estadoTexto} correctamente.");
    }
}
