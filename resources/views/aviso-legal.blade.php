<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAWSCAPE | Aviso Legal, Política de Privacidad y RGPD</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #00ff88;
            --green-dim: #00cc6a;
            --red: #ff2244;
            --yellow: #ffcc00;
            --bg: #020c06;
            --panel: #041a0e;
            --panel-alt: #051f12;
            --border: #00ff8840;
            --border-solid: #00ff8866;
            --text: #c8ffdf;
            --text-secondary: #8abf9e;
        }

        * { box-sizing: border-box; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Share Tech Mono', monospace;
            padding: 2rem;
            line-height: 1.7;
            margin: 0;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,255,136,0.013) 2px, rgba(0,255,136,0.013) 4px);
            pointer-events: none;
            z-index: 1000;
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
            background: var(--panel);
            border: 1px solid var(--border);
            padding: 3rem;
            position: relative;
            box-shadow: 0 0 40px rgba(0, 255, 136, 0.05), inset 0 0 80px rgba(0, 255, 136, 0.01);
        }

        .container::before {
            content: '// DOCUMENTO_LEGAL v2.0 — CLASIFICACIÓN: PÚBLICO';
            position: absolute;
            top: -0.7rem; left: 1rem;
            background: var(--bg);
            padding: 0 0.5rem;
            color: var(--green);
            font-size: 0.75rem;
            letter-spacing: 0.15em;
        }

        .doc-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .doc-header h1 {
            font-family: 'Orbitron', sans-serif;
            color: var(--green);
            font-size: 1.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin: 0 0 0.5rem 0;
        }

        .doc-header .subtitle {
            color: var(--text-secondary);
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }

        .doc-meta {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.8rem 1rem;
            background: var(--panel-alt);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .doc-meta span strong { color: var(--green-dim); }

        .toc {
            background: var(--panel-alt);
            border: 1px solid var(--border);
            padding: 1.2rem 1.5rem;
            margin-bottom: 2.5rem;
        }

        .toc-title {
            color: var(--green);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.8rem;
        }

        .toc ol {
            margin: 0;
            padding-left: 1.5rem;
            list-style: none;
            counter-reset: toc-counter;
        }

        .toc ol li { counter-increment: toc-counter; margin-bottom: 0.3rem; }

        .toc ol li::before {
            content: counter(toc-counter, decimal-leading-zero) '.';
            color: var(--green-dim);
            margin-right: 0.5rem;
            font-weight: bold;
        }

        .toc a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .toc a:hover { color: var(--green); }

        h2 {
            color: var(--green);
            font-size: 1.15rem;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid rgba(0, 255, 136, 0.15);
        }

        h2 .section-num {
            color: var(--green-dim);
            margin-right: 0.3rem;
        }

        h3 {
            color: var(--green-dim);
            font-size: 1rem;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
        }

        p {
            margin-bottom: 1.2rem;
            color: #b0e0bf;
            text-align: justify;
            text-shadow: 0 0 4px rgba(200, 255, 223, 0.08);
        }

        ul, ol {
            color: #b0e0bf;
            padding-left: 1.2rem;
            margin-bottom: 1.2rem;
        }

        li {
            margin-bottom: 0.7rem;
            line-height: 1.6;
        }

        li strong { color: var(--green); }

        .alert-box {
            border-left: 4px solid var(--red);
            background: rgba(255, 34, 68, 0.05);
            padding: 1rem 1.5rem;
            margin: 2rem 0;
        }

        .alert-box p {
            color: #ff8899;
            margin: 0;
            font-size: 0.95rem;
        }

        .info-box {
            border-left: 4px solid var(--yellow);
            background: rgba(255, 204, 0, 0.04);
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
        }

        .info-box p {
            color: #ffe580;
            margin: 0;
            font-size: 0.92rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.2rem 0;
            font-size: 0.9rem;
        }

        .data-table th {
            text-align: left;
            color: var(--green);
            padding: 0.6rem 0.8rem;
            border-bottom: 1px solid var(--border-solid);
            background: var(--panel-alt);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }

        .data-table td {
            padding: 0.6rem 0.8rem;
            border-bottom: 1px solid rgba(0, 255, 136, 0.08);
            color: #b0e0bf;
            vertical-align: top;
        }

        .data-table tr:hover td { background: rgba(0, 255, 136, 0.03); }

        .legal-basis {
            display: inline-block;
            background: rgba(0, 255, 136, 0.08);
            color: var(--green-dim);
            padding: 0.15rem 0.5rem;
            font-size: 0.8rem;
            border: 1px solid var(--border);
        }

        .footer-legal {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.8rem;
        }

        .btn-back {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.8rem 2rem;
            border: 1px solid var(--green);
            color: var(--green);
            text-decoration: none;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.05em;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: var(--green);
            color: var(--bg);
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.4);
        }

        @media (max-width: 600px) {
            .container { padding: 1.5rem; }
            .doc-header h1 { font-size: 1.3rem; }
            body { padding: 0.8rem; }
            .doc-meta { flex-direction: column; font-size: 0.75rem; }
            .data-table { font-size: 0.8rem; }
            .data-table th, .data-table td { padding: 0.4rem 0.5rem; }
        }
    </style>
</head>
<body>

<div class="container">

    {{-- ═══════════════════════ CABECERA ═══════════════════════ --}}
    <div class="doc-header">
        <h1>Aviso Legal y Política de Privacidad</h1>
        <div class="subtitle">Escape Room Educativo Digital — DAWSCAPE</div>
    </div>

    <div class="doc-meta">
        <span><strong>Titular:</strong> Alumnos 1º DAW — XTART</span>
        <span><strong>Vigencia:</strong> Curso 2025/2026</span>
        <span><strong>Última revisión:</strong> {{ now()->format('d/m/Y') }}</span>
    </div>

    {{-- ═══════════════════════ ÍNDICE ═══════════════════════ --}}
    <div class="toc">
        <div class="toc-title">// Índice del Documento</div>
        <ol>
            <li><a href="#s1">Información general y titularidad</a></li>
            <li><a href="#s2">Objeto y naturaleza de la plataforma</a></li>
            <li><a href="#s3">Propiedad intelectual e industrial</a></li>
            <li><a href="#s4">Protección de datos personales (RGPD / LOPDGDD)</a></li>
            <li><a href="#s5">Derechos de los interesados</a></li>
            <li><a href="#s6">Política de cookies</a></li>
            <li><a href="#s7">Medidas de seguridad</a></li>
            <li><a href="#s8">Condiciones de uso y participación</a></li>
            <li><a href="#s9">Limitación de responsabilidad</a></li>
            <li><a href="#s10">Legislación aplicable y jurisdicción</a></li>
        </ol>
    </div>

    {{-- ═══════════════════════ SECCIONES ═══════════════════════ --}}

    <h2 id="s1"><span class="section-num">01.</span> Información General y Titularidad</h2>
    <p>
        En cumplimiento de la <em>Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSICE)</em>, se facilitan los siguientes datos identificativos del proyecto:
    </p>
    <table class="data-table">
        <tr><th>Campo</th><th>Detalle</th></tr>
        <tr><td>Denominación</td><td><strong>DAWSCAPE</strong> — Escape Room Educativo Digital</td></tr>
        <tr><td>Titularidad</td><td>Alumnos del Ciclo Formativo de Grado Superior en Desarrollo de Aplicaciones Web (1º DAW)</td></tr>
        <tr><td>Centro educativo</td><td>XTART — Centro de Formación Profesional</td></tr>
        <tr><td>Curso académico</td><td>2025 / 2026</td></tr>
        <tr><td>Contacto</td><td>A través de la dirección del centro o los profesores responsables del módulo</td></tr>
        <tr><td>Naturaleza</td><td>Proyecto académico sin ánimo de lucro</td></tr>
    </table>

    <h2 id="s2"><span class="section-num">02.</span> Objeto y Naturaleza de la Plataforma</h2>
    <p>
        <strong>DAWSCAPE</strong> (en adelante, la "Plataforma") es una aplicación web de <em>escape room</em> educativo desarrollada íntegramente por alumnado de 1º DAW como proyecto del módulo de <em>Desarrollo Web en Entorno Servidor</em>. Su finalidad es exclusivamente <strong>académica, formativa y de demostración técnica</strong> de las competencias adquiridas durante el ciclo formativo.
    </p>
    <p>
        La Plataforma no constituye una actividad comercial, no persigue fines de lucro y su uso está limitado al ámbito del centro educativo XTART y a las actividades organizadas por este.
    </p>

    <h2 id="s3"><span class="section-num">03.</span> Propiedad Intelectual e Industrial</h2>
    <p>
        La totalidad del código fuente, diseño gráfico, mecánicas de juego, textos, efectos de sonido, estructura de base de datos y demás elementos que componen la Plataforma son propiedad intelectual de sus autores (alumnos de 1º DAW — XTART), protegidos por la <em>Ley de Propiedad Intelectual (Real Decreto Legislativo 1/1996)</em> y distribuidos bajo licencia <strong>MIT</strong>.
    </p>

    <div class="alert-box">
        <p><strong>AVISO_</strong> Queda prohibida la reproducción, distribución, comunicación pública o transformación no autorizada de los elementos de esta Plataforma que no estén cubiertos por los términos de la licencia MIT, especialmente de aquellos contenidos originales protegidos por derechos de autor.</p>
    </div>

    <h2 id="s4"><span class="section-num">04.</span> Protección de Datos Personales</h2>
    <p>
        En cumplimiento del <em>Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo, de 27 de abril de 2016, relativo a la protección de las personas físicas en lo que respecta al tratamiento de datos personales (RGPD)</em> y de la <em>Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD)</em>, se informa al usuario de los siguientes extremos:
    </p>

    <h3>4.1 Responsable del tratamiento</h3>
    <p>
        El tratamiento de datos es realizado bajo la supervisión del profesorado responsable del módulo en el centro educativo XTART, quien actúa como responsable del tratamiento a efectos del artículo 4.7 del RGPD.
    </p>

    <h3>4.2 Datos recabados</h3>
    <p>La Plataforma recoge exclusivamente los siguientes datos personales durante el proceso de inscripción y participación:</p>
    <table class="data-table">
        <tr><th>Dato</th><th>Tipo</th><th>Obligatorio</th></tr>
        <tr><td>Correo electrónico institucional</td><td>Identificativo (@estudiante.xtart.com)</td><td>Sí</td></tr>
        <tr><td>Nombre del equipo</td><td>Seudónimo colectivo</td><td>Sí</td></tr>
        <tr><td>Ciclo y grupo</td><td>Clasificación académica</td><td>Sí</td></tr>
    </table>

    <div class="info-box">
        <p><strong>NOTA:</strong> No se recaban datos especialmente protegidos (art. 9 RGPD). El nombre de equipo es un seudónimo elegido libremente y no identifica a personas físicas de forma directa. No se recopilan datos biométricos, de localización ni fotografías.</p>
    </div>

    <h3>4.3 Finalidad y base jurídica</h3>
    <table class="data-table">
        <tr><th>Finalidad</th><th>Base legal (art. 6 RGPD)</th></tr>
        <tr><td>Registro e identificación de equipos participantes</td><td><span class="legal-basis">6.1.a — Consentimiento</span></td></tr>
        <tr><td>Control de acceso y progreso en la actividad</td><td><span class="legal-basis">6.1.a — Consentimiento</span></td></tr>
        <tr><td>Registro de tiempos y elaboración del ranking</td><td><span class="legal-basis">6.1.a — Consentimiento</span></td></tr>
        <tr><td>Envío del código de acceso por correo electrónico</td><td><span class="legal-basis">6.1.b — Ejecución contractual</span></td></tr>
        <tr><td>Gestión de incidencias técnicas y auditoría interna</td><td><span class="legal-basis">6.1.f — Interés legítimo</span></td></tr>
    </table>

    <h3>4.4 Plazo de conservación</h3>
    <p>
        Los datos personales se conservarán exclusivamente durante el periodo que dure la actividad académica y el plazo de evaluación del proyecto (curso 2025/2026). Una vez finalizado, se procederá a la <strong>eliminación completa e irreversible</strong> de todos los registros de la base de datos que contengan datos personales, en un plazo máximo de <strong>30 días naturales</strong> tras la fecha de fin de la actividad.
    </p>

    <h3>4.5 Comunicación de datos a terceros</h3>
    <p>
        Los datos personales <strong>no serán cedidos, vendidos ni transferidos</strong> a ninguna entidad pública o privada fuera del ámbito estrictamente académico de la actividad. Los únicos destinatarios son:
    </p>
    <ul>
        <li><strong>Resend (servicio de email transaccional):</strong> utilizado exclusivamente para el envío del código de acceso al correo del equipo. Se transmite únicamente la dirección de correo electrónico, conforme a su <a href="https://resend.com/legal/privacy-policy" style="color: var(--green-dim);" target="_blank" rel="noopener noreferrer">política de privacidad</a>.</li>
    </ul>
    <p>
        No se realizan transferencias internacionales de datos fuera del Espacio Económico Europeo (EEE) salvo las derivadas del uso de Resend, que opera con cláusulas contractuales tipo aprobadas por la Comisión Europea.
    </p>

    <h2 id="s5"><span class="section-num">05.</span> Derechos de los Interesados</h2>
    <p>
        De conformidad con los artículos 15 a 22 del RGPD, cualquier participante puede ejercer los siguientes derechos:
    </p>
    <ul>
        <li><strong>Acceso</strong> (art. 15) — Obtener confirmación de si se tratan sus datos y acceder a ellos.</li>
        <li><strong>Rectificación</strong> (art. 16) — Solicitar la corrección de datos inexactos o incompletos.</li>
        <li><strong>Supresión</strong> (art. 17) — Solicitar la eliminación de sus datos ("derecho al olvido").</li>
        <li><strong>Limitación del tratamiento</strong> (art. 18) — Solicitar que se restrinja el tratamiento en determinadas circunstancias.</li>
        <li><strong>Portabilidad</strong> (art. 20) — Recibir sus datos en un formato estructurado y de uso común.</li>
        <li><strong>Oposición</strong> (art. 21) — Oponerse al tratamiento de sus datos en cualquier momento.</li>
    </ul>
    <p>
        Para ejercer cualquiera de estos derechos, el usuario deberá dirigirse al profesorado responsable del módulo a través de los canales de comunicación oficiales del centro educativo XTART, identificándose adecuadamente.
    </p>

    <div class="info-box">
        <p><strong>DERECHO DE RECLAMACIÓN:</strong> Si considera que el tratamiento de sus datos vulnera la normativa vigente, tiene derecho a presentar una reclamación ante la <em>Agencia Española de Protección de Datos</em> (AEPD) — <a href="https://www.aepd.es" style="color: var(--yellow);" target="_blank" rel="noopener noreferrer">www.aepd.es</a>.</p>
    </div>

    <h2 id="s6"><span class="section-num">06.</span> Política de Cookies</h2>
    <p>
        La Plataforma utiliza exclusivamente <strong>cookies técnicas de sesión</strong>, imprescindibles para el correcto funcionamiento del sistema. Estas cookies no requieren consentimiento expreso conforme al artículo 22.2 de la LSSICE, al tratarse de cookies estrictamente necesarias para la prestación del servicio.
    </p>
    <table class="data-table">
        <tr><th>Cookie</th><th>Finalidad</th><th>Duración</th><th>Tipo</th></tr>
        <tr><td><code style="color: var(--green);">dawscape_session</code></td><td>Mantener la sesión de juego activa e identificar el progreso del equipo</td><td>Sesión (se elimina al cerrar el navegador)</td><td>Técnica / Necesaria</td></tr>
        <tr><td><code style="color: var(--green);">XSRF-TOKEN</code></td><td>Protección contra ataques de falsificación de solicitudes (CSRF)</td><td>Sesión</td><td>Seguridad / Necesaria</td></tr>
    </table>
    <p>
        <strong>No se utilizan</strong> cookies publicitarias, de análisis comportamental ni de rastreo de terceros. La Plataforma no integra servicios de analítica externa (Google Analytics, Meta Pixel, etc.).
    </p>
    <p>
        Adicionalmente, el sistema utiliza <code style="color: var(--green);">localStorage</code> del navegador para almacenar preferencias de audio (volumen, estado de reproducción). Estos datos se almacenan exclusivamente en el dispositivo del usuario y no se transmiten a ningún servidor.
    </p>

    <h2 id="s7"><span class="section-num">07.</span> Medidas de Seguridad</h2>
    <p>
        En cumplimiento del artículo 32 del RGPD, se han implementado las medidas técnicas y organizativas apropiadas para garantizar un nivel de seguridad adecuado al riesgo, incluyendo:
    </p>
    <ul>
        <li><strong>Protección CSRF:</strong> Todas las solicitudes de escritura están protegidas mediante tokens de verificación.</li>
        <li><strong>Validación de entrada:</strong> Los datos de usuario se validan y sanitizan en el servidor antes de cualquier operación.</li>
        <li><strong>Limitación de tasa:</strong> Se aplica <em>rate limiting</em> en endpoints sensibles para prevenir ataques de fuerza bruta.</li>
        <li><strong>Hashing de contraseñas:</strong> Las credenciales de administración se almacenan con algoritmos de hash seguros (bcrypt).</li>
        <li><strong>Control de acceso:</strong> El panel de administración está protegido mediante autenticación y middleware de verificación de roles.</li>
        <li><strong>Progreso lineal:</strong> Un middleware personalizado impide el salto no autorizado entre pruebas del juego.</li>
    </ul>

    <h2 id="s8"><span class="section-num">08.</span> Condiciones de Uso y Participación</h2>
    <p>
        El acceso y uso de la Plataforma implica la aceptación íntegra de las presentes condiciones. En particular, el usuario se compromete a:
    </p>
    <ul>
        <li>Utilizar la Plataforma exclusivamente para la finalidad prevista (participación en el escape room educativo).</li>
        <li>No intentar acceder a áreas restringidas, manipular el cronómetro, alterar datos de otros equipos ni explotar vulnerabilidades técnicas.</li>
        <li>No utilizar herramientas automatizadas, <em>scripts</em> o cualquier medio que interfiera con el normal funcionamiento del sistema.</li>
        <li>Proporcionar datos veraces durante el proceso de inscripción.</li>
        <li>Respetar las normas de convivencia del centro educativo durante el desarrollo de la actividad.</li>
    </ul>
    <p>
        El incumplimiento de estas condiciones podrá dar lugar a la <strong>descalificación inmediata</strong> del equipo y a las medidas disciplinarias que el centro educativo estime oportunas.
    </p>

    <h2 id="s9"><span class="section-num">09.</span> Limitación de Responsabilidad</h2>
    <p>
        Los creadores de la Plataforma no serán responsables de:
    </p>
    <ul>
        <li>Interrupciones del servicio debidas a fallos técnicos, cortes de conexión, mantenimiento o causas de fuerza mayor.</li>
        <li>Problemas derivados del dispositivo, navegador o red del usuario que afecten a la experiencia de juego.</li>
        <li>Alteraciones en el cronómetro o en el progreso del juego causadas por factores externos al sistema.</li>
        <li>Daños o perjuicios de cualquier naturaleza derivados del uso indebido de la Plataforma por parte del usuario.</li>
    </ul>
    <p>
        La Plataforma se ofrece en su estado actual (<em>"as is"</em>) sin garantías de ningún tipo, expresas o implícitas, más allá de las exigidas por la legislación vigente.
    </p>

    <h2 id="s10"><span class="section-num">10.</span> Legislación Aplicable y Jurisdicción</h2>
    <p>
        Las presentes condiciones se rigen por la legislación española vigente. Para cualquier controversia que pudiera derivarse del acceso o uso de la Plataforma, las partes se someten a los Juzgados y Tribunales del domicilio del centro educativo, con renuncia expresa a cualquier otro fuero que pudiera corresponderles.
    </p>

    {{-- ═══════════════════════ PIE ═══════════════════════ --}}
    <div class="footer-legal">
        <p style="color: var(--text-secondary); font-size: 0.8rem;">
            Documento generado para el proyecto DAWSCAPE — XTART — Curso Académico 2025/2026<br>
            Última actualización: {{ now()->format('d \d\e F \d\e Y') }}
        </p>
        <a href="{{ route('inscripcion.mostrar') }}" class="btn-back">← Volver a Inscripción</a>
    </div>

</div>

</body>
</html>
