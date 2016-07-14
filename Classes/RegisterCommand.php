<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class RegisterCommand extends \PhpSlackBot\Command\BaseCommand
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    protected function configure() {
        $this->setName('register');
    }

    protected function execute($message, $context) {
        var_dump("context");
        var_dump($this->getCurrentUser());
        if($this->pool->register($this->getCurrentUser())){
            $this->send($this->getCurrentChannel(), null, 'You are now registered');
        } else {
            $this->send($this->getCurrentChannel(), null, 'You are already registered to play');
        }
    }


}