<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Venta;
use PDF; // DomPDF

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
            subject: 'Venta Realizada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
        $pdf = PDF::loadView('pdf.boleta_venta', ['venta' => $this->venta]);

        return $this->subject('Boleta de Venta')
            ->view('emails.venta_realizada') // correo html
            ->attachData($pdf->output(), 'boleta_venta_' . $this->venta->id . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
