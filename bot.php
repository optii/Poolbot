<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Classes/RegisterCommand.php';
require __DIR__ . '/Classes/AcceptCommand.php';
require __DIR__ . '/Classes/LeaderboardCommand.php';
require __DIR__ . '/Classes/LostCommand.php';
require __DIR__ . '/Classes/CancelCommand.php';
require __DIR__ . '/Classes/ChallengeCommand.php';
require __DIR__ . '/Classes/AddAdminCommand.php';
require __DIR__ . '/Classes/SeasonCommand.php';
require __DIR__ . '/Classes/PreviousSeasonLeaderboardCommand.php';
require __DIR__ . '/Classes/HelpCommand.php';
require __DIR__ . '/Classes/MatchesCommand.php';
require __DIR__ . '/Classes/GifCommand.php';
require __DIR__ . '/Classes/PoolbotBaseCommand.php';
require __DIR__ . '/Classes/Pool.php';

use PhpSlackBot\Bot;


$bot = new Bot();
$pool = Pool::create();
$bot->setToken('xoxb-5azef6546429041-qTnojioijieCpokpopkqa7'); // Get your token here https://my.slack.com/services/new/bot
$bot->loadCommand(new RegisterCommand($pool));
$bot->loadCommand(new LeaderboardCommand($pool));
$bot->loadCommand(new ChallengeCommand($pool));
$bot->loadCommand(new AddAdminCommand($pool));
$bot->loadCommand(new LostCommand($pool));
$bot->loadCommand(new CancelCommand($pool));
$bot->loadCommand(new SeasonCommand($pool));
$bot->loadCommand(new PreviousSeasonLeaderboardCommand($pool));
$bot->loadCommand(new MatchesCommand($pool));
$bot->loadCommand(new GifCommand($pool));
$bot->loadCommand(new HelpCommand());
$bot->loadCommand(new AcceptCommand($pool));
$bot->run();
