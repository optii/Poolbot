<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class ChallengeCommand extends PoolbotBaseCommand
{
    protected function configure() {
        $this->setName('challenge');
    }

      protected function execute($message, $context) {
        $t = $this->parseCommand($message);
        if($user2 = $t){
            $res = $this->pool->challenge($this->getCurrentUser(), $user2);
            if($res === true){
                $this->send($this->getCurrentChannel(), null, '<@'.$this->getCurrentUser().'> has challenged <@'.$user2.'> to a match', 'challange');
            } else {
                $this->send($this->getCurrentChannel(), null, $res);
            }


        } else {
            $this->send($this->getCurrentChannel(), null, 'Syntax error: challenge @username');
        }
    }

    private function parseCommand($message)
    {
        $arguments = explode(' ', $message['text']);
        if(count($arguments) != 2){
            return false;
        }

        return Pool::parseUser($arguments[1]);
    }
}
