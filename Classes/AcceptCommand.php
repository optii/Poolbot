<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class AcceptCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('accept');
    }

    protected function execute($message, $context) {
        $this->logger->info('Command '.get_class(), $message);

        if($this->pool->isRegistered($this->getCurrentUser())){
            if($this->pool->accept($this->getCurrentUser())){
                $this->send($this->getCurrentChannel(), null, 'You have accepted the challenge', 'accepted');
            } else {
                $this->send($this->getCurrentChannel(), null, 'You have no challenges to accept');
            }
        } else {
            $this->send($this->getCurrentChannel(), null, 'You must be registered to access the bot');
        }
    }

}
