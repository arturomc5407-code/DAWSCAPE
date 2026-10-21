<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $preguntas = [
            // Ciencia
            ['enunciado' => '¿Qué planeta es conocido como el “planeta rojo”?', 'respuesta_correcta' => 'Marte'],
            ['enunciado' => '¿Cómo se llama el proceso por el cual el agua pasa de estado líquido a gaseoso?', 'respuesta_correcta' => 'Evaporacion, Evaporación'],
            ['enunciado' => '¿Qué tipo de animal es una ballena azul?', 'respuesta_correcta' => 'Mamifero, Mamífero'],
            ['enunciado' => '¿Cuál es el órgano más grande del cuerpo humano?', 'respuesta_correcta' => 'Piel'],

            // Historia
            ['enunciado' => '¿En qué país comenzó la Revolución Industrial?', 'respuesta_correcta' => 'Inglaterra, Reino Unido'],
            ['enunciado' => '¿Qué emperador romano fue asesinado en los Idus de marzo?', 'respuesta_correcta' => 'Julio Cesar, Julio César'],
            ['enunciado' => '¿Cómo se llamaba el primer viaje tripulado que llegó a la Luna?', 'respuesta_correcta' => 'Apolo 11, Apollo 11'],

            // Geografía
            ['enunciado' => '¿En qué continente se encuentra la cordillera de los Andes?', 'respuesta_correcta' => 'America del Sur, Sudamerica, Sudamérica'],
            ['enunciado' => '¿Cuál es el país más grande del mundo en superficie?', 'respuesta_correcta' => 'Rusia'],
            ['enunciado' => '¿Qué océano baña las costas de Portugal y Galicia?', 'respuesta_correcta' => 'Atlantico, Atlántico'],

            // Tecnología
            ['enunciado' => '¿Qué significa la sigla “HTTP” en una dirección web?', 'respuesta_correcta' => 'Hypertext Transfer Protocol'],
            ['enunciado' => '¿Qué empresa creó el sistema operativo Android?', 'respuesta_correcta' => 'Google'],
            ['enunciado' => '¿Cómo se llama el lenguaje estándar para estructurar páginas web?', 'respuesta_correcta' => 'HTML'],

            // Deporte
            ['enunciado' => '¿Cuántos minutos dura un partido de fútbol profesional sin contar el tiempo añadido?', 'respuesta_correcta' => '90'],
            ['enunciado' => '¿En qué deporte destacó especialmente Michael Jordan?', 'respuesta_correcta' => 'Baloncesto'],

            // Entretenimiento
            ['enunciado' => '¿Cómo se llama la saga de películas en la que aparece el personaje Darth Vader?', 'respuesta_correcta' => 'Star Wars, La Guerra de las Galaxias, Guerra de las Galaxias'],
            ['enunciado' => '¿Qué superhéroe es conocido como “El Caballero Oscuro”?', 'respuesta_correcta' => 'Batman'],
            ['enunciado' => '¿Qué serie de televisión popular transcurre en el reino de Poniente?', 'respuesta_correcta' => 'Juego de Tronos, Game of Thrones'],

            // Miscelánea
            ['enunciado' => '¿Qué instrumento musical tiene teclas blancas y negras y se toca con ambas manos?', 'respuesta_correcta' => 'Piano'],
            ['enunciado' => '¿Qué día se celebra mundialmente el Día de la Tierra?', 'respuesta_correcta' => '22 de abril, 22 abril'],
        ];

        foreach ($preguntas as $pregunta) {
            DB::table('pregunta_dinamicas')->updateOrInsert(
                ['enunciado' => $pregunta['enunciado']],
                [
                    'respuesta_correcta' => $pregunta['respuesta_correcta'],
                    'activa'             => true,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $enunciados = [
            '¿Qué planeta es conocido como el “planeta rojo”?',
            '¿Cómo se llama el proceso por el cual el agua pasa de estado líquido a gaseoso?',
            '¿Qué tipo de animal es una ballena azul?',
            '¿Cuál es el órgano más grande del cuerpo humano?',
            '¿En qué país comenzó la Revolución Industrial?',
            '¿Qué emperador romano fue asesinado en los Idus de marzo?',
            '¿Cómo se llamaba el primer viaje tripulado que llegó a la Luna?',
            '¿En qué continente se encuentra la cordillera de los Andes?',
            '¿Cuál es el país más grande del mundo en superficie?',
            '¿Qué océano baña las costas de Portugal y Galicia?',
            '¿Qué significa la sigla “HTTP” en una dirección web?',
            '¿Qué empresa creó el sistema operativo Android?',
            '¿Cómo se llama el lenguaje estándar para estructurar páginas web?',
            '¿Cuántos minutos dura un partido de fútbol profesional sin contar el tiempo añadido?',
            '¿En qué deporte destacó especialmente Michael Jordan?',
            '¿Cómo se llama la saga de películas en la que aparece el personaje Darth Vader?',
            '¿Qué superhéroe es conocido como “El Caballero Oscuro”?',
            '¿Qué serie de televisión popular transcurre en el reino de Poniente?',
            '¿Qué instrumento musical tiene teclas blancas y negras y se toca con ambas manos?',
            '¿Qué día se celebra mundialmente el Día de la Tierra?',
        ];

        DB::table('pregunta_dinamicas')
            ->whereIn('enunciado', $enunciados)
            ->delete();
    }
};

