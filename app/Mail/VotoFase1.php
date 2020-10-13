<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Annata;
use App\Voto;
use App\Candidato;

class VotoFase1 extends Mailable
{
    use Queueable, SerializesModels;

	public $user;
	public $voto;
	public $segnalazioni;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,Voto $voto,$segnalazioni)
    {
        $this->user = $user;
        $this->voto = $voto;
        $this->segnalazioni = $segnalazioni;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Premio Italia '.$this->voto->anno.' - Voto fase 1')->view('emails.emailVoto1');
    }
}
