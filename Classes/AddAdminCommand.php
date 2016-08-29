<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class AddAdminCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('admin');
    }

    protected function execute($message, $context) {
        $this->logger->info('Command '.get_class(), $message);
       if($this->pool->isRegistered($this->getCurrentUser())){
           $res = $this->pool->addAdmin($this->getCurrentUser(), $this->parseCommand($message));
           if($res === true){
               $this->send($this->getCurrentChannel(), null, 'You are now an admin', 'power');
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
