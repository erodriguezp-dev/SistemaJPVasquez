<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Procesa la solicitud de inicio de sesión con throttle y verificación de estado.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ], [
            'usuario.required' => 'El campo usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $throttleKey = Str::lower($request->input('usuario')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'usuario' => "Demasiados intentos de acceso. Intente nuevamente en {$seconds} segundos.",
            ])->onlyInput('usuario');
        }

        // Buscar el usuario para verificar si existe e identificar su estado
        $user = User::where('usuario', $request->input('usuario'))->first();

        if ($user && ! $user->activo) {
            RateLimiter::hit($throttleKey);
            return back()->withErrors([
                'usuario' => 'Su cuenta se encuentra inactiva. Contacte al administrador del sistema.',
            ])->onlyInput('usuario');
        }

        $credentials = [
            'usuario' => $request->input('usuario'),
            'password' => $request->input('password'),
            'activo' => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        RateLimiter::hit($throttleKey);

        return back()->withErrors([
            'usuario' => 'Las credenciales ingresadas son incorrectas.',
        ])->onlyInput('usuario');
    }

    /**
     * Cierra la sesión del usuario actual.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Ha cerrado sesión correctamente.');
    }
}
