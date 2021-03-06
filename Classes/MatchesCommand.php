<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class MatchesCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('matches');
    }

    protected function execute($message, $context) {
        $this->logger->info('Command '.get_class(), $message);
        $this->send($this->getCurrentChannel(), null, $this->pool->getCurrentMatches());
    }

}
