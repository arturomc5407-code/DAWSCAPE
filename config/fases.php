<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fases del juego (centralizado)
    |--------------------------------------------------------------------------
    | - "db": valor almacenado en partidas.fase_actual
    | - "nombre": nombre completo para mostrar en UI (Top10, admin, etc.)
    | - "peso": usado para ordenar por progreso
    */
    'fases' => [
        'inicio' => [
            'db' => 'INICIO',
            'nombre' => 'En el Inicio',
            'peso' => 0,
        ],
        'prueba1' => [
            'db' => 'Prueba 1 Mario',
            'nombre' => 'Prueba 1 — Criptografía',
            'peso' => 1,
        ],
        'prueba2' => [
            'db' => 'Prueba 2 Yoseth',
            'nombre' => 'Prueba 2 — Servidor Seguro',
            'peso' => 2,
        ],
        'prueba3' => [
            'db' => 'Prueba 3 Angel',
            'nombre' => 'Prueba 3 — Máquina de Café',
            'peso' => 3,
        ],
        'prueba4' => [
            'db' => 'Prueba 4 Arturo',
            'nombre' => 'Prueba 4 — Oasis Snacks',
            'peso' => 4,
        ],
        'prueba5' => [
            'db' => 'Prueba 5 Eloy',
            'nombre' => 'Prueba 5 — Protocolo Espejo',
            'peso' => 5,
        ],
        'prueba6' => [
            'db' => 'Prueba 6 Migue',
            'nombre' => 'Prueba 6 — Descifrado Final',
            'peso' => 6,
        ],
        'prueba7' => [
            'db' => 'Prueba 7 Dinamica',
            'nombre' => 'Prueba 7 — El Códice Oculto',
            'peso' => 7,
        ],
        'prueba8' => [
            'db' => 'Prueba 8 Cultura General',
            'nombre' => 'Prueba 8 — Cultura General',
            'peso' => 8,
        ],
        'prueba9' => [
            'db' => 'Prueba 9 Juego de Pistas',
            'nombre' => 'Prueba 9 — Juego de Pistas',
            'peso' => 9,
        ],
        'finalizado' => [
            'db' => 'FINALIZADO',
            'nombre' => 'Finalizado',
            'peso' => 10,
        ],
        'timeout' => [
            'db' => 'FINALIZADO_TIMEOUT',
            'nombre' => 'Tiempo Agotado',
            'peso' => 10,
        ],
    ],

    // Último peso "en juego" antes de finalizar (para barra de progreso)
    'max_peso_en_juego' => 9,
];

