<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Annata;

class SollecitoInvio extends Mailable
{
    use Queueable, SerializesModels;

	public $user;
	public $annata;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Annata $annata)
    {
        $this->user = $user;
        $this->annata = $annata;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Premio Italia - Invia il tuo voto')->view('emails.sollecitoInvio')->text('emails.sollecitoInvio_text');
    }
}
