<?php

use \rfreebern\Giphy;

abstract class PoolbotBaseCommand extends \PhpSlackBot\Command\BaseCommand
{
    protected $pool;
    protected $logger;

  public function __construct(Pool $pool, \Katzgrau\KLogger\Logger $logger)
  {
      $this->pool = $pool;
      $this->logger = $logger;
  }

  protected function send($channel, $username, $message, $tag = null){
     if($this->pool->isGif()){
       $giphy = new \rfreebern\Giphy();
       $result = $giphy->trending(1);
       $message .= "\n ".$result->data->image_original_url;
     }

     parent::send($channel, $username, $message);
  }


}
