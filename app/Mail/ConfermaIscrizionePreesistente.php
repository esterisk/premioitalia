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

class ConfermaIscrizionePreesistente extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $richiedente;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $richiedente)
    {
        $this->user = $user;
        $this->richiedente = $richiedente;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Conferma iscrizione al voto al Premio Italia',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function build()
    {
        return $this->subject('Conferma iscrizione al voto al Premio Italia')->view('emails.confermaIscrizionePreesistente')->text('emails.confermaIscrizionePreesistente_text');
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