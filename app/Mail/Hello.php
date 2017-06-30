<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;


class Hello extends Mailable
{
    use Queueable, SerializesModels;
    
    //Sender Name
    public $senderName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $this->senderName = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = config('mail.username');

        $subject = "Hi,".$this->senderName. '. Now, '. Auth::user()->name . " is inviting you." ;

        return $this->from($from)->subject($subject)->markdown('emails.hello')
                    ->with([
                        'Receiver'=> $this->senderName,
                        'From' => Auth::user()->name
                    ]);
    }
}
