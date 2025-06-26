<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf; // DomPDF

class VentaRealizada extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $venta;

    public function __construct(Venta $venta)
    {
        $this->venta = $venta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Gracias por tu compra Venta Realizada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'correos.venta_realizada',
            with: ['venta' => $this->venta]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generar PDF desde una vista
       /*  $pdf = PDF::loadView('pdf.boleta_venta', ['venta' => $this->venta]);

        return $this->subject('Boleta de Venta')
            ->view('correos.venta_realizada') // correo html vista opcional para contenido del email
            ->attachData($pdf->output(), 'boleta_venta_' . $this->venta->id . '.pdf', [
                'mime' => 'application/pdf',
            ]); */


        // Generar PDF desde una vista
        // puede usar este método fromData si ha generado un PDF en memoria y
        // desea adjuntarlo al correo electrónico sin escribirlo en el disco
        $pdf = Pdf::loadView('pdf.boleta_venta', ['venta' => $this->venta]);
        return [
            Attachment::fromData(fn () => $pdf->output(), 'boleta_venta_'.$this->venta->id.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
