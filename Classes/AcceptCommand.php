<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
require_once(dirname(__FILE__).'/PoolbotBaseCommand.php');

class AcceptCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('accept');
    }

    protected function execute($message, $context) {
        if($this->pool->isRegistered($this->getCurrentUser())){
            if($this->pool->accept($this->getCurrentUser())){
                $this->send($this->getCurrentChannel(), null, 'You have accepted the challenge');
            } else {
                $this->send($this->getCurrentChannel(), null, 'You have no challenges to accept');
            }
        } else {
            $this->send($this->getCurrentChannel(), null, 'You must be registered to access the bot');
        }
    }

}
