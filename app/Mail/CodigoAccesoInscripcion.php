<?php

namespace App\Mail;

use App\Models\Inscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoAccesoInscripcion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Inscripcion $inscripcion
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->inscripcion->correo],
            subject: 'DAWSCAPE - Tu Código de Acceso al Escape Room',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.codigo-acceso',
            with: [
                'nombreEquipo' => $this->inscripcion->nombre_equipo,
                'codigoAcceso' => $this->inscripcion->codigo_acceso,
                'inscripcion'  => $this->inscripcion,
            ],
        );
    }
}
