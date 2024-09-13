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
    public $subject;

    public function __construct($detalhes, $subject)
    {
        $this->detalhes = $detalhes;
        $this->subject = $subject;
    }

    public function build()
    {
        return $this->from('admin@aibnet.online')
                    ->subject($this->subject)
                    ->view('emails.contact_email')
                    ->with('detalhes', $this->detalhes);
    }
}
