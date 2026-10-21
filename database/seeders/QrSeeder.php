<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseUrl = rtrim(config('app.url', 'https://dawscape.fun'), '/');

        $qrs = [
            [
                'nombre'        => 'Máquina de Café',
                'descripcion'   => 'QR pegado en la máquina de café del hall. Contiene la pista de la Prueba 3.',
                'fase_requerida' => 'PRUEBA_3',
                'url'           => $baseUrl . '/qr/maquina-cafe-001',
            ],
            [
                'nombre'        => 'Máquina Oasis (Snacks)',
                'descripcion'   => 'QR pegado en la máquina de snacks de la cantina. Contiene la pista de la Prueba 4.',
                'fase_requerida' => 'PRUEBA_4',
                'url'           => $baseUrl . '/qr/maquina-snacks-001',
            ],
            [
                'nombre'        => 'Taquillas',
                'descripcion'   => 'QR escondido en la zona de las taquillas. Contiene la pista de la Prueba 5.',
                'fase_requerida' => 'PRUEBA_5',
                'url'           => $baseUrl . '/qr/taquillas-001',
            ],
        ];

        foreach ($qrs as $qr) {
            \App\Models\Qr::create($qr);
        }
    }
}
