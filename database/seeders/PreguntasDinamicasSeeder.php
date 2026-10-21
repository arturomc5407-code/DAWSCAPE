<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder principal de preguntas de cultura general - Prueba 8
 * 70 preguntas con respuestas alternativas
 *
 * Ejecutar en el servidor después de git pull:
 * php artisan db:seed --class=PreguntasDinamicasSeeder
 *
 * Seguro para ejecutar múltiples veces: usa updateOrCreate
 * para no duplicar preguntas y actualizar alternativas si cambian.
 */
class PreguntasDinamicasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preguntas = [
            // GEOGRAFÍA – ENIGMAS (15 preguntas)
            ['enunciado' => 'Muchos creen que mi ciudad más famosa es Sídney… pero la política vive en otra.', 'respuesta_correcta' => 'Canberra', 'respuestas_alternativas' => ['canberra']],
            ['enunciado' => 'Nací en África y vi crecer civilizaciones a mis orillas. Algunos me disputan el título, pero sigo siendo el más largo.', 'respuesta_correcta' => 'Nilo', 'respuestas_alternativas' => ['nilo', 'rio nilo', 'el nilo', 'el rio nilo']],
            ['enunciado' => 'Tierra de faraones, pirámides y el Nilo. Estoy en el continente donde nació la humanidad.', 'respuesta_correcta' => 'África', 'respuestas_alternativas' => ['africa', 'el continente africano', 'continente africano']],
            ['enunciado' => 'Mi silueta parece un zapato elegante listo para pisar el Mediterráneo.', 'respuesta_correcta' => 'Italia', 'respuestas_alternativas' => ['italia', 'italy', 'la italia', 'republica italiana']],
            ['enunciado' => 'Soy tan grande que abarco once husos horarios.', 'respuesta_correcta' => 'Rusia', 'respuestas_alternativas' => ['rusia', 'russia', 'la rusia', 'federacion rusa']],
            ['enunciado' => 'Separan dos países vecinos y forman frontera natural (España y Francia).', 'respuesta_correcta' => 'Pirineos', 'respuestas_alternativas' => ['pirineos', 'los pirineos', 'montes pirineos', 'cordillera de los pirineos']],
            ['enunciado' => 'Baña Nueva York y separa América de Europa.', 'respuesta_correcta' => 'Atlántico', 'respuestas_alternativas' => ['atlantico', 'oceano atlantico', 'el atlantico', 'el oceano atlantico', 'atlantic']],
            ['enunciado' => 'Mi nombre parece el de una antigua tribu indígena, pero soy capital del país del arce.', 'respuesta_correcta' => 'Ottawa', 'respuestas_alternativas' => ['ottawa', 'otawa', 'otava']],
            ['enunciado' => 'Tengo chocolate, relojes y neutralidad… pero no euro.', 'respuesta_correcta' => 'Suiza', 'respuestas_alternativas' => ['suiza', 'switzerland', 'la suiza', 'confederacion suiza']],
            ['enunciado' => 'Soy el mayor desierto cálido y me encuentro en la parte superior de un continente.', 'respuesta_correcta' => 'Sahara', 'respuestas_alternativas' => ['sahara', 'desierto del sahara', 'el sahara', 'norte de africa', 'africa', 'sahara desert']],
            ['enunciado' => 'Capital tecnológica y hogar del sol naciente.', 'respuesta_correcta' => 'Tokio', 'respuestas_alternativas' => ['tokio', 'tokyo', 'toquio']],
            ['enunciado' => 'Cruzo la ciudad del amor bajo torres de hierro.', 'respuesta_correcta' => 'Sena', 'respuestas_alternativas' => ['sena', 'rio sena', 'el sena', 'el rio sena', 'seine']],
            ['enunciado' => 'Capital del tango y del mate.', 'respuesta_correcta' => 'Buenos Aires', 'respuestas_alternativas' => ['buenos aires', 'buenosaires', 'bsas', 'bs as', 'baires']],
            ['enunciado' => 'El techo del mundo, en el Himalaya.', 'respuesta_correcta' => 'Everest', 'respuestas_alternativas' => ['everest', 'monte everest', 'mount everest', 'el everest', 'el monte everest']],
            ['enunciado' => 'Chichén Itzá y Tikal hablan de mí.', 'respuesta_correcta' => 'México', 'respuestas_alternativas' => ['mexico', 'mejico', 'estados unidos mexicanos']],

            // HISTORIA – ENIGMAS (15 preguntas)
            ['enunciado' => 'El año en que Europa volvió a arder tras la invasión de Polonia.', 'respuesta_correcta' => '1939', 'respuestas_alternativas' => ['1939', 'año 1939', 'en 1939']],
            ['enunciado' => 'Primer líder de la nación de las barras y estrellas.', 'respuesta_correcta' => 'George Washington', 'respuestas_alternativas' => ['george washington', 'washington', 'jorge washington', 'g washington', 'g. washington']],
            ['enunciado' => 'Civilización que levantó las pirámides de Giza.', 'respuesta_correcta' => 'Egipcia', 'respuestas_alternativas' => ['egipcia', 'egipto', 'los egipcios', 'egipcios', 'civilizacion egipcia', 'antiguo egipto']],
            ['enunciado' => 'Año en que tres carabelas cambiaron el mundo.', 'respuesta_correcta' => '1492', 'respuestas_alternativas' => ['1492', 'año 1492', 'en 1492']],
            ['enunciado' => 'El muro que dividía ideologías cayó en este año.', 'respuesta_correcta' => '1989', 'respuestas_alternativas' => ['1989', 'año 1989', 'en 1989']],
            ['enunciado' => 'Gobernó España con mano firme hasta 1975.', 'respuesta_correcta' => 'Francisco Franco', 'respuestas_alternativas' => ['francisco franco', 'franco', 'el caudillo', 'general franco', 'francisco franco bahamonde']],
            ['enunciado' => 'Libertad, igualdad y fraternidad nacieron en…', 'respuesta_correcta' => 'Revolucion Francesa', 'respuestas_alternativas' => ['revolucion francesa', 'la revolucion francesa', 'francesa', 'revolucion', 'francia', 'la revolucion', 'french revolution']],
            ['enunciado' => 'El tratado que castigó a Alemania tras la Gran Guerra.', 'respuesta_correcta' => 'Tratado de Versalles', 'respuestas_alternativas' => ['tratado de versalles', 'versalles', 'el tratado de versalles', 'paz de versalles']],
            ['enunciado' => 'Imperio que dominaba Tenochtitlán.', 'respuesta_correcta' => 'Azteca', 'respuestas_alternativas' => ['azteca', 'imperio azteca', 'aztecas', 'los aztecas', 'mexica', 'mexicas', 'los mexicas']],
            ['enunciado' => 'Descubrí por accidente el primer antibiótico.', 'respuesta_correcta' => 'Alexander Fleming', 'respuestas_alternativas' => ['alexander fleming', 'fleming', 'sir alexander fleming', 'a fleming', 'a. fleming']],
            ['enunciado' => 'Carta Magna española aprobada tras la dictadura.', 'respuesta_correcta' => '1978', 'respuestas_alternativas' => ['1978', 'año 1978', 'en 1978']],
            ['enunciado' => 'Durante la II Guerra Mundial no reinaba Isabel II, sino…', 'respuesta_correcta' => 'Jorge VI', 'respuestas_alternativas' => ['jorge vi', 'jorge 6', 'rey jorge vi', 'jorge sexto', 'george vi', 'king george vi']],
            ['enunciado' => 'Civilización andina que construyó Machu Picchu.', 'respuesta_correcta' => 'Inca', 'respuestas_alternativas' => ['inca', 'incas', 'imperio inca', 'los incas', 'civilizacion inca', 'imperio incaico']],
            ['enunciado' => 'Año en que España se dividió en guerra fratricida.', 'respuesta_correcta' => '1936', 'respuestas_alternativas' => ['1936', 'año 1936', 'en 1936']],
            ['enunciado' => 'El Imperio Romano de Occidente cayó en el año…', 'respuesta_correcta' => '476', 'respuestas_alternativas' => ['476', '476 dc', '476 d c', '476 d.c.', 'año 476']],

            // CIENCIA – ENIGMAS (15 preguntas)
            ['enunciado' => 'Gigante gaseoso con gran mancha roja.', 'respuesta_correcta' => 'Jupiter', 'respuestas_alternativas' => ['jupiter', 'el jupiter', 'planeta jupiter', 'jupiter planeta']],
            ['enunciado' => 'Gas invisible que mantiene tu vida en cada respiración.', 'respuesta_correcta' => 'Oxigeno', 'respuestas_alternativas' => ['oxigeno', 'o2', 'el oxigeno', 'oxygen']],
            ['enunciado' => 'Metal precioso cuyo símbolo químico es Au.', 'respuesta_correcta' => 'Oro', 'respuestas_alternativas' => ['oro', 'au', 'el oro', 'gold']],
            ['enunciado' => 'Número de huesos que sostiene tu cuerpo adulto.', 'respuesta_correcta' => '206', 'respuestas_alternativas' => ['206', '206 huesos', 'doscientos seis']],
            ['enunciado' => 'Bombeo constante, sin descanso.', 'respuesta_correcta' => 'Corazon', 'respuestas_alternativas' => ['corazon', 'el corazon', 'heart']],
            ['enunciado' => 'Proceso verde que transforma luz en vida.', 'respuesta_correcta' => 'Fotosintesis', 'respuestas_alternativas' => ['fotosintesis', 'la fotosintesis', 'photosynthesis']],
            ['enunciado' => 'Revolucioné la física con la relatividad.', 'respuesta_correcta' => 'Albert Einstein', 'respuestas_alternativas' => ['albert einstein', 'einstein', 'a einstein', 'a. einstein']],
            ['enunciado' => 'La obtienes tomando el sol.', 'respuesta_correcta' => 'Vitamina D', 'respuestas_alternativas' => ['vitamina d', 'd', 'la vitamina d', 'vit d', 'vit. d']],
            ['enunciado' => 'Detecto los movimientos y latidos de la Tierra.', 'respuesta_correcta' => 'Sismografo', 'respuestas_alternativas' => ['sismografo', 'el sismografo', 'sismometro', 'seismografo']],
            ['enunciado' => 'Soy la unidad mínima que tiene vida.', 'respuesta_correcta' => 'Celula', 'respuestas_alternativas' => ['celula', 'la celula', 'cell']],
            ['enunciado' => 'Dos de hidrógeno y uno de oxígeno forman...', 'respuesta_correcta' => 'H2O', 'respuestas_alternativas' => ['h2o', 'agua', 'water', 'el agua', 'h 2 o']],
            ['enunciado' => 'El animal más veloz en tierra firme.', 'respuesta_correcta' => 'Guepardo', 'respuestas_alternativas' => ['guepardo', 'cheetah', 'el guepardo', 'chita']],
            ['enunciado' => 'Metal que es líquido a temperatura ambiente.', 'respuesta_correcta' => 'Mercurio', 'respuestas_alternativas' => ['mercurio', 'el mercurio', 'hg', 'mercury']],
            ['enunciado' => 'Compañera nocturna y solitaria de la Tierra.', 'respuesta_correcta' => 'Luna', 'respuestas_alternativas' => ['luna', 'la luna', 'moon', 'the moon']],
            ['enunciado' => 'Estoy en el mismísimo centro del átomo.', 'respuesta_correcta' => 'Nucleo', 'respuestas_alternativas' => ['nucleo', 'el nucleo', 'nucleo atomico', 'el nucleo atomico', 'nucleus']],

            // LITERATURA Y ARTE – ENIGMAS (10 preguntas)
            ['enunciado' => 'Caballero que luchó contra gigantes que en realidad eran molinos.', 'respuesta_correcta' => 'Miguel de Cervantes', 'respuestas_alternativas' => ['miguel de cervantes', 'cervantes', 'miguel cervantes', 'miguel de cervantes saavedra', 'cervantes saavedra', 'm de cervantes', 'm. de cervantes']],
            ['enunciado' => 'Sonrisa enigmática pintada por…', 'respuesta_correcta' => 'Leonardo da Vinci', 'respuestas_alternativas' => ['leonardo da vinci', 'da vinci', 'leonardo', 'leonardo davinci', 'davinci', 'leonardo de vinci']],
            ['enunciado' => 'Amor trágico entre Montescos y Capuletos fue escrito por...', 'respuesta_correcta' => 'William Shakespeare', 'respuestas_alternativas' => ['william shakespeare', 'shakespeare', 'w shakespeare', 'w. shakespeare', 'guillermo shakespeare']],
            ['enunciado' => 'Artista atormentado que perdió parte de su oreja.', 'respuesta_correcta' => 'Vincent van Gogh', 'respuestas_alternativas' => ['vincent van gogh', 'van gogh', 'gogh', 'vincent vangogh', 'vangogh', 'v van gogh', 'v. van gogh']],
            ['enunciado' => 'Movimiento artístico onírico de Salvador Dalí.', 'respuesta_correcta' => 'Surrealismo', 'respuestas_alternativas' => ['surrealismo', 'el surrealismo', 'surrealista', 'surrealism']],
            ['enunciado' => 'Realismo mágico en Macondo ideado por...', 'respuesta_correcta' => 'Gabriel Garcia Marquez', 'respuestas_alternativas' => ['gabriel garcia marquez', 'garcia marquez', 'gabo', 'gabriel garcia', 'g garcia marquez', 'g. garcia marquez', 'marquez']],
            ['enunciado' => 'Drama ambientado en una casa andaluza autor de…', 'respuesta_correcta' => 'Federico Garcia Lorca', 'respuestas_alternativas' => ['federico garcia lorca', 'garcia lorca', 'lorca', 'federico lorca', 'f garcia lorca', 'f. garcia lorca']],
            ['enunciado' => 'Museo parisino que guarda la Mona Lisa.', 'respuesta_correcta' => 'Museo del Louvre', 'respuestas_alternativas' => ['museo del louvre', 'louvre', 'el louvre', 'musee du louvre', 'the louvre']],
            ['enunciado' => 'Compuso la Novena con el "Himno a la Alegría".', 'respuesta_correcta' => 'Ludwig van Beethoven', 'respuestas_alternativas' => ['ludwig van beethoven', 'beethoven', 'l van beethoven', 'l. van beethoven', 'van beethoven']],
            ['enunciado' => 'Detective de Baker Street creado por…', 'respuesta_correcta' => 'Arthur Conan Doyle', 'respuestas_alternativas' => ['arthur conan doyle', 'conan doyle', 'doyle', 'sir arthur conan doyle', 'a conan doyle', 'a. conan doyle']],

            // CINE Y TELEVISIÓN – ENIGMAS (5 preguntas)
            ['enunciado' => 'Director que hundió el Titanic en la ficción de 1997.', 'respuesta_correcta' => 'James Cameron', 'respuestas_alternativas' => ['james cameron', 'cameron', 'j cameron', 'j. cameron']],
            ['enunciado' => 'El niño que sobrevivió pertenece a la saga mágica de…', 'respuesta_correcta' => 'Harry Potter', 'respuestas_alternativas' => ['harry potter', 'potter', 'hp', 'harrypotter']],
            ['enunciado' => 'El rey de la sabana animada clásica.', 'respuesta_correcta' => 'Simba', 'respuestas_alternativas' => ['simba', 'el rey leon', 'rey leon', 'the lion king']],
            ['enunciado' => 'Profesor de química que se volvió criminal.', 'respuesta_correcta' => 'Breaking Bad', 'respuestas_alternativas' => ['breaking bad', 'breakingbad', 'bb']],
            ['enunciado' => 'Superhéroe neoyorquino mordido por una araña radiactiva.', 'respuesta_correcta' => 'Spider-Man', 'respuestas_alternativas' => ['spider-man', 'spiderman', 'spider man', 'el hombre araña', 'hombre araña', 'el hombre arana', 'hombre arana']],

            // DEPORTES – ENIGMAS (5 preguntas)
            ['enunciado' => 'Número de guerreros en el campo por equipo en fútbol (profesional).', 'respuesta_correcta' => '11', 'respuestas_alternativas' => ['11', 'once', 'xi']],
            ['enunciado' => 'Cada cuántos años arde la antorcha olímpica.', 'respuesta_correcta' => '4', 'respuestas_alternativas' => ['4', 'cuatro', '4 años', 'cada 4 años', 'cada cuatro años']],
            ['enunciado' => 'Selección campeona del mundo en 2010.', 'respuesta_correcta' => 'Espana', 'respuestas_alternativas' => ['espana', 'españa', 'spain', 'la roja', 'seleccion espanola', 'seleccion española']],
            ['enunciado' => 'Deporte de tierra batida dominado por Rafa Nadal.', 'respuesta_correcta' => 'Tenis', 'respuestas_alternativas' => ['tenis', 'tennis', 'el tenis']],
            ['enunciado' => 'Valor máximo de un tiro lejano detrás de la línea en baloncesto.', 'respuesta_correcta' => '3', 'respuestas_alternativas' => ['3', 'tres', 'iii', '3 puntos', 'tres puntos']],

            // EXTRA DIFÍCILES (5 preguntas)
            ['enunciado' => 'Color Azul + Color Amarillo resultan en...', 'respuesta_correcta' => 'Verde', 'respuestas_alternativas' => ['verde', 'el verde', 'green', 'color verde']],
            ['enunciado' => 'Número romano con forma de ángulo recto que equivale a cincuenta.', 'respuesta_correcta' => 'L', 'respuestas_alternativas' => ['l', '50', 'cincuenta']],
            ['enunciado' => 'El país exótico del Taj Mahal.', 'respuesta_correcta' => 'India', 'respuestas_alternativas' => ['india', 'la india', 'republica de la india']],
            ['enunciado' => 'El instrumento que mide la temperatura corporal y ambiental.', 'respuesta_correcta' => 'Termometro', 'respuestas_alternativas' => ['termometro', 'el termometro', 'thermometer']],
            ['enunciado' => 'El continente más pequeño y rodeado de agua de la Tierra.', 'respuesta_correcta' => 'Oceania', 'respuestas_alternativas' => ['oceania', 'australia', 'la oceania', 'continente oceania']],
        ];

        foreach ($preguntas as $pregunta) {
            \App\Models\PreguntaDinamica::updateOrCreate(
                ['enunciado' => $pregunta['enunciado']],
                [
                    'respuesta_correcta' => $pregunta['respuesta_correcta'],
                    'respuestas_alternativas' => $pregunta['respuestas_alternativas'],
                    'activa' => true,
                ]
            );
        }
    }
}