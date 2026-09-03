<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicazioneRegistrata extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Il template base delle email si aspetta la variabile $user con il nome del destinatario.
     */
    public $user;

    public $application;

    public $convention;

    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->user = $application;
        $this->convention = $application->convention;
    }

    /**
     * Get the message content definition.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Richiesta di iscrizione al voto del Premio Italia')
            ->view('emails.applicazioneRegistrata')
            ->text('emails.applicazioneRegistrata_text');
    }
}
