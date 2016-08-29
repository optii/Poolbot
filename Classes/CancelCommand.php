<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class CancelCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('cancel');
    }

    protected function execute($message, $context) {
        $this->logger->info('Command '.get_class(), $message);
       if($this->pool->isRegistered($this->getCurrentUser())){
           if($this->pool->cancel($this->getCurrentUser())){
               $this->send($this->getCurrentChannel(), null, 'Match has been cancelled', 'void');
           } else {
               $this->send($this->getCurrentChannel(), null, 'There is no match to cancel', 'no match');
           }

       } else {
           $this->send($this->getCurrentChannel(), null, 'You are not registered');
       }
    }

}
