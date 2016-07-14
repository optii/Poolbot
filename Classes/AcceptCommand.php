<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class AcceptCommand extends \PhpSlackBot\Command\BaseCommand
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

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