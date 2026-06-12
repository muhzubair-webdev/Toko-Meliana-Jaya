<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class LowStockAlert extends Mailable
{

    /**
     * The collection of low-stock products.
     */
    public Collection $products;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $products)
    {
        $this->products = $products;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $count = $this->products->count();
        return new Envelope(
            subject: "⚠️ Peringatan Stok Menipis — {$count} Produk Perlu Restock",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-alert',
        );
    }
}
