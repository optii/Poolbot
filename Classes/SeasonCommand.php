<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
require(dirname(__FILE__).'/PoolbotBaseCommand.php');

class SeasonCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('season');
    }

    protected function execute($message, $context) {
       if($this->pool->isRegistered($this->getCurrentUser()) && $this->pool->isAdmin($this->getCurrentUser())){
            $this->pool->newSeason();
           $this->send($this->getCurrentChannel(), null, "A new season has now been started");
       } else {
           $this->send($this->getCurrentChannel(), null, "You must be an admin to execute this command");
       }
    }

}
