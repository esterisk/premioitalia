<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Annata;

class SpecialNotice extends Mailable
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
        return $this->subject('Premio Italia - Invio errato, sorry')->view('emails.specialNotice')->text('emails.specialNotice_text');
    }
}