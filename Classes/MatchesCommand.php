<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
require_once(dirname(__FILE__).'/PoolbotBaseCommand.php');

class MatchesCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('matches');
    }

    protected function execute($message, $context) {
        $this->send($this->getCurrentChannel(), null, $this->pool->getCurrentMatches());
    }

}
