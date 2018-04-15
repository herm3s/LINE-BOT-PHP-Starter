<?php
echo "หวัดดีประชาชน(hmph)(hmph)(hmph)(cony kiss)";


$ch = curl_init();
// Disable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);
// Will dump a beauty json :3
//var_dump(json_decode($result, true));
$data = json_decode($result,true);
echo  "THB :" ; echo $data["THB"];
