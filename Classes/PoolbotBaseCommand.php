<?php

use \rfreebern\Giphy;

abstract class PoolbotBaseCommand extends \PhpSlackBot\Command\BaseCommand
{
  protected $pool;

  public function __construct(Pool $pool)
  {
      $this->pool = $pool;
  }

  protected function send($channel, $username, $message, $tag = null){
     if($this->pool->isGif()){
       $giphy = new \rfreebern\Giphy();
       $result = $giphy->random($tag);
       $message .= "\n ".$result->data->image_original_url;
     }

     parent::send($channel, $username, $message);
  }


}
