<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatEventPusherWhatsapp implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $user_id;
  public $id;
  public $whatsapp_bot_subscriber_subscriber_id;
  public $whatsapp_bot_id;
  public $sender;
  public $message_content;  
  public $message_content_html;
  public $conversation_time;
  public $last_conversation_message;  
  public $agent_name;

  public function __construct($user_id,$id,$whatsapp_bot_subscriber_subscriber_id,$whatsapp_bot_id,$sender,$message_content,$message_content_html,$conversation_time,$last_conversation_message,$agent_name){
      $this->user_id=$user_id;
      $this->id=$id;
      $this->whatsapp_bot_subscriber_subscriber_id=$whatsapp_bot_subscriber_subscriber_id;
      $this->whatsapp_bot_id=$whatsapp_bot_id;
      $this->sender=$sender;
      $this->message_content=$message_content;      
      $this->message_content_html=$message_content_html;
      $this->conversation_time=$conversation_time;
      $this->last_conversation_message=$last_conversation_message;      
      $this->agent_name=$agent_name;
  }

  public function broadcastOn(){

      $channel_name="whatsapp-my-channel-{$this->user_id}";
      return [$channel_name];
  }

  public function broadcastAs(){
      return 'whatsapp-chat-event-pusher';
  }
}
