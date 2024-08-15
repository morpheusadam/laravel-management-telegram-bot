<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SimpleHtmlEmail;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $param_to;
    protected $param_name;
    protected $param_message;
    protected $param_subject;
    protected $config;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($param_to,$param_name,$param_message,$param_subject,$config=null)
    {
        $this->param_to = $param_to;
        $this->param_name = $param_name;
        $this->param_message = $param_message;
        $this->param_subject = $param_subject;
        $this->config = $config;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {       
       if(isset($this->config['mail'])) config(['mail'=>$this->config['mail']]);       
       if(isset($this->config['app'])) config(['app'=>$this->config['app']]);     
       Mail::to($this->param_to)->send(new SimpleHtmlEmail($this->param_name,$this->param_message,$this->param_subject,$this->config));
    }
}
