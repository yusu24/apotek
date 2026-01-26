<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;

    /**
     * Create a new message instance.
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Struk Pembelian - ' . $this->sale->invoice_no,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt',
            with: [
                'sale' => $this->sale,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Generate PDF receipt
        $pdf = Pdf::loadView('pdf.receipt', ['sale' => $this->sale]);
        
        return [
            Attachment::fromData(fn () => $pdf->output(), 'struk-' . $this->sale->invoice_no . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
