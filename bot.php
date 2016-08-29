<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/optii/glicko2-php/class.Glicko2Player.php';
require_once __DIR__ . '/Classes/Season.php';
require_once __DIR__ . '/Classes/Match.php';
require_once __DIR__ . '/Classes/PoolbotBaseCommand.php';
require_once __DIR__ . '/Classes/RegisterCommand.php';
require_once __DIR__ . '/Classes/AcceptCommand.php';
require_once __DIR__ . '/Classes/LeaderboardCommand.php';
require_once __DIR__ . '/Classes/LostCommand.php';
require_once __DIR__ . '/Classes/CancelCommand.php';
require_once __DIR__ . '/Classes/ChallengeCommand.php';
require_once __DIR__ . '/Classes/AddAdminCommand.php';
require_once __DIR__ . '/Classes/SeasonCommand.php';
require_once __DIR__ . '/Classes/PreviousSeasonLeaderboardCommand.php';
require_once __DIR__ . '/Classes/HelpCommand.php';
require_once __DIR__ . '/Classes/MatchesCommand.php';
require_once __DIR__ . '/Classes/GifCommand.php';
require_once __DIR__ . '/Classes/Pool.php';

use PhpSlackBot\Bot;

$config = parse_ini_file(__DIR__ . '/Config/config.ini');
$logger = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
$logger->info('Poolbot starting');
$bot = new Bot();
$pool = Pool::create();
$bot->setToken($config['token']); // Get your token here https://my.slack.com/services/new/bot
$bot->loadCommand(new RegisterCommand($pool, $logger));
$bot->loadCommand(new LeaderboardCommand($pool, $logger));
$bot->loadCommand(new ChallengeCommand($pool, $logger));
$bot->loadCommand(new AddAdminCommand($pool, $logger));
$bot->loadCommand(new LostCommand($pool, $logger));
$bot->loadCommand(new CancelCommand($pool, $logger));
$bot->loadCommand(new SeasonCommand($pool, $logger));
$bot->loadCommand(new PreviousSeasonLeaderboardCommand($pool, $logger));
$bot->loadCommand(new MatchesCommand($pool, $logger));
$bot->loadCommand(new GifCommand($pool, $logger));
$bot->loadCommand(new HelpCommand());
$bot->loadCommand(new AcceptCommand($pool, $logger));
$bot->run();