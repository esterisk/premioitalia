<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Annata;
use App\Models\Voto;
use App\Models\Candidato;

class VotoFase2 extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $voto;
    public $preferenze;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Voto $voto, $preferenze)
    {
        $this->user = $user;
        $this->voto = $voto;
        $this->preferenze = $preferenze;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Premio Italia ' . $this->voto->anno . ' - Voto fase 2')->view('emails.emailVoto2');
    }
}