# Poolbot
Slack poolbot written in PHP without a database but with persistent data

## Install

 To install the bot, clone this repository change the following line in bot.php and add your token:
 
 ```
 $bot->setToken('xoxb-5azefa429041-qTazefazefaze65846qa7');
 ```
 
 Then just run the bot:
 
 ```
 php bot.php
 ```
 
 The bot will then issue the password required to register as an admin, this password will change each time the bot is restarted

## Commands

 * **help**: List the commands of the bot
 * **register**: Registers the user to the bot
 * **challenge @user**: Challenges a user to a match
 * **cancel**: Cancels any ongoing challenges
 * **accept**: Accepts a challenge
 * **lost**: Records a defeat, automatically accepts the challenge if not previously accepted
 * **leaderboard**: Shows the leaderboard for the current season
 * **previous _number_**: Shows the leaderboard for the given season
 * **admin _password_**: Registers the user as an admin, requires the password
 * **season**: Starts a new season, user must be admin
 
## Credits
 
 This bot uses https://github.com/jclg/php-slack-bot 
