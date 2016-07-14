<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class LostCommand extends \PhpSlackBot\Command\BaseCommand
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    protected function configure() {
        $this->setName('lost');
    }

    protected function execute($message, $context) {
       if($this->pool->isRegistered($this->getCurrentUser())){
            if($this->pool->lost($this->getCurrentUser())){
                $this->send($this->getCurrentChannel(), null, '<@'.$this->getCurrentUser().'> lost! HAHAHA!');
            } else {
                $this->send($this->getCurrentChannel(), null, 'There does not seem to be a match');
            }
       } else {
           $this->send($this->getCurrentChannel(), null, 'You are not registered');
       }
    }

}