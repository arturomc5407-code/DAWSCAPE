<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking Top 20 - DAWSCAPE</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a2b3c;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #1a2b3c;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        /* MEJORADO: Subtitle con mejor contraste para PDF */
        .subtitle {
            font-size: 14px;
            color: #334155;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        /* MEJORADO: Table header con mejor contraste */
        th {
            background-color: #f8fafc;
            color: #334155;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            font-size: 14px;
            color: #1e293b;
        }
        .pos {
            font-weight: bold;
            color: #0f172a;
            width: 50px;
            text-align: center;
        }
        .time {
            font-family: monospace;
            font-weight: bold;
            color: #059669;
        }
        /* MEJORADO: Footer con mejor contraste */
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="title">DAWSCAPE</h1>
        <div class="subtitle">Reporte Oficial: Mejores 20 Equipos Clasificados</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align:center">Posición</th>
                <th>Nombre del Equipo</th>
                <th>Correo Electrónico</th>
                <th>Tiempo Final</th>
            </tr>
        </thead>
        <tbody>
            @foreach($top20 as $index => $partida)
            <tr>
                <td class="pos">{{ $index + 1 }}º</td>
                <td style="font-weight: bold">{{ $partida->nombre_equipo }}</td>
                <td style="color: #475569">{{ $partida->email }}</td>
                <td class="time">
                    {{ floor((int)$partida->tiempo_segundos / 60) }} min {{ (int)$partida->tiempo_segundos % 60 }} seg
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ date('d/m/Y H:i:s') }} — DAWSCAPE Admin System
    </div>

</body>
</html>
