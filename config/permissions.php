<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Matriz de Permisos del Sistema
    |--------------------------------------------------------------------------
    | Define qué roles tienen acceso a cada módulo y acción en la aplicación.
    | Roles válidos: 'administrador', 'gerente_proyecto', 'contador', 'colaborador'
    */

    'roles' => [
        'administrador' => 'Administrador',
        'gerente_proyecto' => 'Gerente de Proyecto',
        'contador' => 'Contador',
        'colaborador' => 'Colaborador',
    ],

    'modules' => [
        'usuarios' => [
            'ver' => ['administrador'],
            'crear' => ['administrador'],
            'editar' => ['administrador'],
            'cambiar_estado' => ['administrador'],
        ],
        'inventario' => [
            'ver' => ['administrador', 'gerente_proyecto', 'contador', 'colaborador'],
            'registrar' => ['administrador', 'colaborador'],
        ],
        'pedidos' => [
            'ver' => ['administrador', 'gerente_proyecto', 'contador', 'colaborador'],
            'registrar' => ['administrador', 'gerente_proyecto', 'colaborador'],
            'editar' => ['administrador', 'gerente_proyecto'],
        ],
        'reportes' => [
            'ver' => ['administrador', 'gerente_proyecto', 'contador'],
        ],
    ],
];
