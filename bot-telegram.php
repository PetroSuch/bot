<?php
required('telegram-bot/api');
$token = "640430644:AAFOS1a9T-mC4WV2uG4ez99xQ5v7dckdwQk";
$bot = new \TelegramBot\Api\Client($token);
// команда для start
$bot->command('start', function ($message) use ($bot) {
    $answer = 'Hello!';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// команда для помощи
$bot->command('help', function ($message) use ($bot) {
    $answer = 'Commands:
/help - вывод справки';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

$bot->run();
?>
