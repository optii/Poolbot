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
       $giphy = new \rfreebern\Giphy('dc6zaTOxFJmzC');
       $results = $giphy->trending(100);
       $gif = $results[array_rand($results->data)];

       $message .= "\n ".$gif->images->original;
     }

     parent::send($channel, $username, $message);
  }


}
