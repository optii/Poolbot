<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class PreviousSeasonLeaderboardCommand extends \PhpSlackBot\Command\BaseCommand
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    protected function configure() {
        $this->setName('previous');
    }

    protected function execute($message, $context) {
        if($this->pool->isRegistered($this->getCurrentUser())){
            $arguments = explode(' ', $message['text']);
            if(count($arguments) != 2){
                $this->send($this->getCurrentChannel(), null, "You must specify a season");
            } else {

                $this->send($this->getCurrentChannel(), null, $this->pool->getLeaderboard($arguments[1]));
            }
        }
    }


}