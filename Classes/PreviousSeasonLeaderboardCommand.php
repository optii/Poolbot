<?php

/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class PreviousSeasonLeaderboardCommand extends PoolbotBaseCommand
{
    protected function configure()
    {
        $this->setName('previous');
    }

    protected function execute($message, $context)
    {
        $this->logger->info('Command ' . get_class(), $message);
        if ($this->pool->isRegistered($this->getCurrentUser())) {
            $arguments = explode(' ', $message['text']);
            if (count($arguments) != 2) {
                $this->send($this->getCurrentChannel(), null, "You must specify a season");
            } else {

                $this->send($this->getCurrentChannel(), null, $this->pool->getLeaderboard($arguments[1]));
            }
        }
    }


}
