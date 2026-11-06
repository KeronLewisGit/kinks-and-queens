<?php

namespace App\Mail;

use App\Models\PreOrder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewRequest extends Mailable
{
    public function __construct(public PreOrder $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New request '.$this->order->reference.' · '.$this->order->title());
    }

    public function content(): Content
    {
        return new Content(text: 'mail.new-request');
    }
}
