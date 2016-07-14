<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class AddAdminCommand extends \PhpSlackBot\Command\BaseCommand
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    protected function configure() {
        $this->setName('admin');
    }

    protected function execute($message, $context) {
       if($this->pool->isRegistered($this->getCurrentUser())){
           $res = $this->pool->addAdmin($this->getCurrentUser(), $this->parseCommand($message));
           if($res === true){
               $this->send($this->getCurrentChannel(), null, 'You are now an admin');
           } else {
               $this->send($this->getCurrentChannel(), null, $res);
           }

       } else {
           $this->send($this->getCurrentChannel(), null, 'You are not registered');
       }
    }

    private function parseCommand($message)
    {
        $arguments = explode(' ', $message['text']);
        if(count($arguments) != 2){
            return false;
        }

        return $arguments[1];
    }
}