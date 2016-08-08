<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:07
 */
class HelpCommand extends \PhpSlackBot\Command\BaseCommand
{
    protected function configure() {
        $this->setName('help');
    }

    protected function execute($message, $context) {
      $this->send($this->getCurrentChannel(), null, "*POOLBOT HELP*\n
       *help*: Brings up this amazing help tool\n
       *register*: Registers the user to the bot\n
       *challenge @user*: Challenges a user to a match\n
       *cancel*: Cancels any ongoing challenges\n
       *accept*: Accepts a challenge\n
       *lost*: Records a defeat, automatically accepts the challenge if not previously accepted\n
       *leaderboard*: Shows the leaderboard for the current season\n
       *previous _number_*: Shows the leaderboard for the given season\n
       *matches*: lists all on-going matches\n
       *gif*: Toggles the GIF mode of the bot, requires admin\n
       *admin _password_*: Registers the user as an admin, requires the password\n
       *season*: Starts a new season, user must be admin\n");
    }

}
