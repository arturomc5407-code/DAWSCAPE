<?php

return [
    'pistas' => [
        1 => [
            'creador' => 'Mario',
            'imagen' => 'mario.png',
            'imagen_habla' => 'mario2.png',
            'texto' => 'Primer acertijo: el número del aula está en la puerta. Segundo: contad los ordenadores de la sala. Tercero: cada ordenador tiene DOS pantallas, así que multiplicad por 2. Cuarto: contad las luces del techo. Quinto: el código secreto está grabado en la placa metálica del proyector que cuelga del techo, copiadlo EXACTO. Sexto: sumad TODO lo descubierto, y del código extraed cada dígito por separado para sumarlos también.',
        ],
        2 => [
            'creador' => 'Yoseth',
            'imagen' => 'yoseth.png',
            'imagen_habla' => 'yoseth2.png',
            'texto' => 'Buscad 5 letras repartidas por las máquinas del edificio (cafetería, vending, etc.). Cada máquina tiene una letra pegada o escondida. Cuando tengáis las 5 letras, reordenadlas para formar el nombre de un municipio de la Región de Murcia conocido por sus melocotones.',
        ],
        3 => [
            'creador' => 'Ángel',
            'imagen' => 'angel.png',
            'imagen_habla' => 'angel2.png',
            'texto' => 'Para sacar la clave, solo tienes que buscar tres números específicos que te dan en el acertijo y sumarlos. Olvídate de la "Seguridad física" y la "Esencia de cítricos", lo que importa son los números que representan: Los botones: fíjate en el primer número que te dan (en este caso es 18). El precio del té: busca el segundo número de la lista (es el 80). El nivel de azúcar: mira el último número (es el 5).',
        ],
        4 => [
            'creador' => 'Arturo',
            'imagen' => 'arturo.png',
            'imagen_habla' => 'arturo2.png',
            'texto' => 'Tenéis que SUMAR tres cosas de las máquinas de la planta baja: 1) El precio en céntimos de la palmera de chocolate (es 130). 2) Contad cuántos tipos de productos diferentes caben en la misma fila donde está la palmera (son 7). 3) Buscad el número del código del producto de la palmera de chocolate (es el 31). Sumad los tres: 130 + 7 + 31 = 168.',
        ],
        5 => [
            'creador' => 'Eloy',
            'imagen' => 'eloy.png',
            'imagen_habla' => 'eloy2.png',
            'texto' => 'La tabla de la pantalla os dice qué letra corresponde a cada taquilla (Z=1, Y=2... A=26). Id a las taquillas en el orden que indica la secuencia (1, 26, 8, 18, 22, 15, 3) y buscad la letra que hay en cada una. Formad la palabra con esas letras en ESE orden. Pista extra: con el alfabeto invertido, taquilla 1=Z, taquilla 26=A, taquilla 8=S...',
        ],
        6 => [
            'creador' => 'Migue',
            'imagen' => 'miguel.png',
            'imagen_habla' => 'miguel2.png',
            'texto' => 'La memoria es frágil cuando trabaja sola. Dividid la carga, compartid el patrón. Cuatro cuadrantes, una mente colectiva. La sincronización es vuestra única salvación.',
        ],
        7 => [
            'creador' => 'Nacho',
            'imagen' => 'nacho.png',
            'imagen_habla' => 'nacho2.png',
            'texto' => 'Pulsad "Ver Ubicación" en cada dígito del candado para saber dónde está el QR. Id a esa ubicación, escaneadlo con el móvil y leed el acertijo: la respuesta siempre es un número del 0 al 9. Cada acertijo describe un número con pistas de ciencia, matemáticas o cultura general. Si no lo sacáis: buscad en Google la pista más concreta del acertijo y el número saldrá solo.',
        ],
    ],
    'penalizacion_pista' => 480, // 480 segundos (8 minutos) por pista

    'respuestas' => [
        'prueba1' => [
            ['115', 'aula 115', 'lab 115'],
            ['16', 'dieciseis'],
            ['32', 'treinta y dos'],
            ['14', 'catorce'],
            ['5x0lh8bx100127t'],
            ['177', 'ciento setenta y siete']
        ],
        // La prueba 2 la maneja el front-end con un anagrama, pero el back valida también
        'prueba2' => 'cieza',
        'prueba3' => 103, // Checksum: 18 botones + 80 cents + 5 azucar
        'prueba4' => 168, // Checksum planta baja: 130 céntimos palmera + 7 tipos producto fila + 31 código palmera
        'prueba5' => 'zasielx', // Palabra espejo
    ],
];
