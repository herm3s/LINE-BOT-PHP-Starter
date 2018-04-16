<?php

include_once('LINEBot.php');

$proxy = 'velodrome.usefixie.com:80';
$proxyauth = 'fixie:bfhX5Zm0ZC3iu84';
$access_token = 'r0AE+qmwBHCFStfXuZIaO8HzNHnF2eJ3O4zOQIzzAqJ1nmEV1XJXnmbP++ei7yRQBujrR48im+iuMUD7kGyOagWaDhQwq2TIuOqR2UIW+L6EoSHC2VGxAFnm4syPBpDhWitZM0FSe249Z1EN3xxqMgdB04t89/1O/w1cDnyilFU=';








// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);


//header('Content-Type: text/html; charset=utf-8');


$url1="http://dwarfpool.com/eth/api?wallet=0xE058B32A21b8C3B5BBfba621B4c94eC834e4BA9e&email=jason_bomb@me.com";

$ch1 = curl_init();
// Disable SSL verification
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch1, CURLOPT_URL,$url1);
// Execute
$result1=curl_exec($ch1);
// Closing
curl_close($ch1);
$data1 = json_decode($result1,true);

$s_date = $data1["last_share_date"];
$datetime = explode(" ", $s_date);
$selectedTime = $datetime [4];
$endTime = strtotime("+420 minutes", strtotime($selectedTime));

$url="https://min-api.cryptocompare.com/data/price?fsym=ETH&tsyms=BTC,USD,THB";
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,$url);
$result=curl_exec($ch);
curl_close($ch);
$data = json_decode($result,true);
//echo  "THB :" ; echo $data["THB"];

$balance = $data1["wallet_balance"];
$thb_rate =  +$data["THB"] ;
$total = +$balance * +$thb_rate;



// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			
			
			
			$text = "\r\n" . "แชร์ครั้งล่าสุด : " . $datetime [0] . " " .$datetime [1] . " " . $datetime [2] . " " . $datetime [3] . " " . date('h:i:s', $endTime) .  "\r\n" .
                                "ยอด ETH : " . $data1["wallet_balance"] . " ETH" . "\r\n" .
                                "คิดเป็นเงินบาท : " . $total . " THB" . "\r\n" .
                                "1 ETH เท่ากับ : " . $data["THB"] . " THB" . "\r\n" .
                                "ยอดขุดใน 24 ชั่วโมง : " . $data1["earning_24_hours"] . " ETH" . "\r\n" .
                                "Error Status : " . $data1["error_code"] . "\r\n" .
                                "ยอดที่ยังไม่ยืนยัน : " . $data1["immature_earning"] . " ETH" . "\r\n"  . $event['source']['userId'];

			// Get replyToken
			$replyToken = $event['replyToken'];

			
			$video = "https://www.quirksmode.org/html5/videos/big_buck_bunny.mp4";
			
			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => $text ,
				//'type' => 'video',
				//'video' => $video
				
				//  "type": "image",
                                //  "originalContentUrl": "https://image.ibb.co/cZ1NSS/test.jpg",
                                //  "previewImageUrl": "https://thumb.ibb.co/dLK97S/test.jpg"
				
				
			];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
                        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
			$result = curl_exec($ch);
			curl_close($ch);

			echo $result . "\r\n";
		}
	}
}
echo "OK";
