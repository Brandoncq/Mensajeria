<?php

namespace App\Mail;

use App\Models\Reporte;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReporteAprobadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reporte;

    public function __construct(Reporte $reporte)
    {
        $this->reporte = $reporte;
    }

    public function build()
    {
        return $this->subject('🚨 Nuevo Reporte Aprobado')
                    ->view('emails.reporte_aprobado');
    }
}
