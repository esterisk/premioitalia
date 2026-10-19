<?php

namespace App\Mail;

use App\Models\Convention;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BenvenutoElettore extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public ?Convention $convention = null) {}

    /**
     * @return $this
     */
    public function build()
    {
        return $this->subject('Benvenuto fra gli elettori del Premio Italia')
            ->view('emails.benvenutoElettore')
            ->text('emails.benvenutoElettore_text');
    }
}
