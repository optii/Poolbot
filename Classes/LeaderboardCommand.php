<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
require(dirname(__FILE__).'/PoolbotBaseCommand.php');

class LeaderboardCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('leaderboard');
    }

    protected function execute($message, $context) {
        $this->send($this->getCurrentChannel(), null, $this->pool->getLeaderboard());
    }

}
