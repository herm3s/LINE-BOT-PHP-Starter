<?php
$proxy = 'velodrome.usefixie.com:80';
$proxyauth = 'fixie:bfhX5Zm0ZC3iu84';
$access_token = 'r0AE+qmwBHCFStfXuZIaO8HzNHnF2eJ3O4zOQIzzAqJ1nmEV1XJXnmbP++ei7yRQBujrR48im+iuMUD7kGyOagWaDhQwq2TIuOqR2UIW+L6EoSHC2VGxAFnm4syPBpDhWitZM0FSe249Z1EN3xxqMgdB04t89/1O/w1cDnyilFU=';

$url = 'https://api.line.me/v1/oauth/verify';

$headers = array('Authorization: Bearer ' . $access_token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
