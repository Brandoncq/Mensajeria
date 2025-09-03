<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevoReporteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nombreMonitor;

    public function __construct($nombreMonitor)
    {
        $this->nombreMonitor = $nombreMonitor;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📋 Nuevo Reporte Pendiente - DECSAC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nuevo-reporte',
            with: [
                'nombreMonitor' => $this->nombreMonitor,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}