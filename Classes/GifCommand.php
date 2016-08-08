<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class GifCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('gif');
    }

    protected function execute($message, $context) {
        if($this->pool->isRegistered($this->getCurrentUser()) && $this->pool->isAdmin($this->getCurrentUser())){
          $this->pool->toggleGif();
          $this->send($this->getCurrentChannel(), null, ($this->pool->isGif()) ? "Gif mode activated" : "Gif mode deactivated");
        } else {
            $this->send($this->getCurrentChannel(), null, 'You must be an admin');
        }
    }

}
