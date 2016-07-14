<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class SeasonCommand extends \PhpSlackBot\Command\BaseCommand
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

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