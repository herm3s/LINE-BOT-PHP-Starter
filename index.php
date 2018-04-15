<?php

include_once('LINEBot.php');

$bot = new \LINE-BOT-PHP-Starter\LINEBot(new CurlHTTPClient('r0AE+qmwBHCFStfXuZIaO8HzNHnF2eJ3O4zOQIzzAqJ1nmEV1XJXnmbP++ei7yRQBujrR48im+iuMUD7kGyOagWaDhQwq2TIuOqR2UIW+L6EoSHC2VGxAFnm4syPBpDhWitZM0FSe249Z1EN3xxqMgdB04t89/1O/w1cDnyilFU='), [
    'channelSecret' => '9f56daa9d7e46e9b82d2081fce3a6dd1'
]);


$user = $bot->getProfile('U127bc358c8b192a31d4be78f8a6d902a');

echo "หวัดดีประชาชน(hmph)(hmph)(hmph)(cony kiss)" . $user;



