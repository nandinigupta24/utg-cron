<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSachinTestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $campData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($campData)
    {
        $this->campData = $campData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.sachinTest');
    }
}
