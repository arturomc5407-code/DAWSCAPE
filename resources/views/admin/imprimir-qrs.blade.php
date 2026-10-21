<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir QRs - DAWSCAPE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .print-btn {
            display: block;
            margin: 0 auto 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
        }
        .back-btn {
            display: block;
            margin: 0 auto 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: fit-content;
        }
        .card {
            background: white;
            border: 2px dashed #ccc;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            text-align: center;
            page-break-inside: avoid; /* Evita que la tarjeta se corte a la mitad al imprimir */
        }
        .card h2 {
            margin-top: 0;
            color: #333;
        }
        .qr-code {
            margin: 20px 0;
        }
        .instruction {
            font-size: 1.1em;
            color: #555;
            background: #f9f9f9;
            padding: 10px;
            border-left: 4px solid #007bff;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-btn, .back-btn {
                display: none;
            }
            .card {
                border: 1px solid #000;
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/admin" class="back-btn">← Volver al Panel</a>
        <button class="print-btn" onclick="window.print()">🖨️ Imprimir QRs (Ctrl + P)</button>

        @foreach($qrs as $id => $qr)
        <div class="card">
            <h2>{{ $qr['titulo'] }}</h2>
            <div class="qr-code">
                {!! $qr['svg'] !!}
            </div>
            <div class="instruction">
                <strong>Instrucción de pegado:</strong><br>
                {{ $qr['instruccion'] }}
            </div>
            <div style="margin-top: 10px; font-size: 0.8em; color: #888;">
                ID Interno: {{ $id }}
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
