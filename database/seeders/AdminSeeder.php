<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seguro para producción: crea o actualiza el usuario administrador principal
     * leyendo las credenciales únicamente desde variables de entorno (.env).
     */
    public function run(): void
    {
        $usuario = env('ADMIN_USUARIO');
        $password = env('ADMIN_PASSWORD');

        if (empty($usuario) || empty($password)) {
            throw new RuntimeException(
                'Las variables de entorno ADMIN_USUARIO y ADMIN_PASSWORD deben estar definidas en el archivo .env para ejecutar AdminSeeder.'
            );
        }

        User::updateOrCreate(
            ['usuario' => $usuario],
            [
                'name' => 'Administrador JP Vásquez',
                'email' => 'administrador@jpvasquez.local',
                'password' => Hash::make($password),
                'rol' => 'administrador',
                'activo' => true,
            ]
        );
    }
}
