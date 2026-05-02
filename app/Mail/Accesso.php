<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Annata;

class Accesso extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $annata;
    public $sollecito;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Annata $annata, $sollecito = false)
    {
        $this->user = $user;
        $this->annata = $annata;
        $this->sollecito = $sollecito;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Accesso al voto al Premio Italia ' . $this->annata->anno)->view('emails.accesso')->text('emails.accesso_text');
    }
}
