<?php

namespace App\Mail;

use App\Models\Convention;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class Invito extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $convention;
    public $register_route;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token, $convention)
    {
        $this->user = $user;
        $this->convention = $convention;
        $this->register_route = URL::signedRoute(
            'iscrizione',
            ['token' => $token]
        );
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Invito al voto al Premio Italia',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function build()
    {
        return $this->subject('Invito al voto al Premio Italia')->view('emails.invito')->text('emails.invito_text');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}