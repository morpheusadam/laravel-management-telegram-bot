<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SimpleHtmlEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $param_name;
    protected $param_message;
    protected $param_subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($param_name,$param_message,$param_subject)
    {
        $this->param_name = $param_name;
        $this->param_message = $param_message;
        $this->param_subject = $param_subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.simple.template',['name'=>$this->param_name,'message'=>$this->param_message])->subject($this->param_subject);
    }
}
