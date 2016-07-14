<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class CancelCommand extends \PhpSlackBot\Command\BaseCommand
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    protected function configure() {
        $this->setName('cancel');
    }

    protected function execute($message, $context) {
       if($this->pool->isRegistered($this->getCurrentUser())){
           $this->pool->cancel($this->getCurrentUser());
           $this->send($this->getCurrentChannel(), null, 'Match has been cancelled');
       } else {
           $this->send($this->getCurrentChannel(), null, 'You are not registered');
       }
    }

}