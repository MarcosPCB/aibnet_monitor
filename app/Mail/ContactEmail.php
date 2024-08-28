<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $detalhes;

    public function __construct($detalhes)
    {
        $this->detalhes = $detalhes;
    }

    public function build()
    {
        return $this->from('admin@aibnet.online')
                    ->subject('Assunto do Email')
                    ->view('emails.contact_email')
                    ->with('detalhes', $this->detalhes);
    }
}
