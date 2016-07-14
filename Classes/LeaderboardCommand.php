<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class LeaderboardCommand extends \PhpSlackBot\Command\BaseCommand
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    protected function configure() {
        $this->setName('leaderboard');
    }

    protected function execute($message, $context) {
        $this->send($this->getCurrentChannel(), null, $this->pool->getLeaderboard());
    }

}