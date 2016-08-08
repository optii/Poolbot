<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
require_once(dirname(__FILE__).'/PoolbotBaseCommand.php');

class GifCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('gif');
    }

    protected function execute($message, $context) {
        if($this->pool->isRegistered($this->getCurrentUser()) && $this->pool->isAdmin($this->getCurrentUser())){
          $gif = $this->pool->toggleGif();
        } else {
            $this->send($this->getCurrentChannel(), null, 'You must be an admin');
        }
    }

}
