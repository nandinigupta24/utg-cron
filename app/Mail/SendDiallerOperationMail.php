<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDiallerOperationMail extends Mailable
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
        return $this->from('intellingreports@intelling.co.uk')->subject('Data allocations for the BPO')->view('emails.diallerOperation2');

        // return $this->from('intellingreports@intelling.co.uk')->subject('Data allocations for the BPO')->view('emails.diallerOperation');
    }
}
