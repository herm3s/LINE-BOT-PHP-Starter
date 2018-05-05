<?php
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
require "vendor/autoload.php";
require_once('vendor/linecorp/line-bot-sdk/line-bot-sdk-tiny/LINEBotTiny.php');
 
$access_token = 'FZldEem8ostD63IqQ5NQ0mZnYHK/NSzQutlkVIFLa9rRzFYQ3SXMvnzr6gM/rrBPK4wdLlSgA8Ba7vOJMajRtzAYouW9l8rQ3xlQeiDlBS48fUbw41nCul84q4NKVpQ53r/5mF4CUx1CNQfS3+iBbwdB04t89/1O/w1cDnyilFU=';
$channelSecret = 'e5d464aa428fdb58ede0e1a877108551';
$pushID = 'C60d524f3fbe6ca9df96e1788e4f4916d';
// กรณีมีการเชื่อมต่อกับฐานข้อมูล
//require_once("dbconnect.php");
 
///////////// ส่วนของการเรียกใช้งาน class ผ่าน namespace
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
 
 
$httpClient = new CurlHTTPClient($access_token);
$bot = new LINEBot($httpClient, array('channelSecret' => $channelSecret));

// คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
$content = file_get_contents('php://input');
 
// แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
$events = json_decode($content, true);



if(!is_null($events)){
    // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
    $replyToken = $events['events'][0]['replyToken'];
    $userID = $events['events'][0]['source']['userId'];
    $sourceType = $events['events'][0]['source']['type'];
    $is_postback = NULL;
    $is_message = NULL;
    if(isset($events['events'][0]) && array_key_exists('message',$events['events'][0])){
        $is_message = true;
        $typeMessage = $events['events'][0]['message']['type'];
        $userMessage = $events['events'][0]['message']['text'];     
        $idMessage = $events['events'][0]['message']['id']; 
    }
    if(isset($events['events'][0]) && array_key_exists('postback',$events['events'][0])){
        $is_postback = true;
        $dataPostback = NULL;
        parse_str($events['events'][0]['postback']['data'],$dataPostback);;
        $paramPostback = NULL;
        if(array_key_exists('params',$events['events'][0]['postback'])){
            if(array_key_exists('date',$events['events'][0]['postback']['params'])){
                $paramPostback = $events['events'][0]['postback']['params']['date'];
            }
            if(array_key_exists('time',$events['events'][0]['postback']['params'])){
                $paramPostback = $events['events'][0]['postback']['params']['time'];
            }
            if(array_key_exists('datetime',$events['events'][0]['postback']['params'])){
                $paramPostback = $events['events'][0]['postback']['params']['datetime'];
            }                       
        }
    }   
    if(!is_null($is_postback)){
        $textReplyMessage = "ข้อความจาก Postback Event Data = ";
        if(is_array($dataPostback)){
            $textReplyMessage.= json_encode($dataPostback);
        }
        if(!is_null($paramPostback)){
            $textReplyMessage.= " \r\nParams = ".$paramPostback;
        }
        $replyData = new TextMessageBuilder($textReplyMessage);     
    }
    if(!is_null($is_message)){
        switch ($typeMessage){
            case 'text':
                $userMessage = strtolower($userMessage); // แปลงเป็นตัวเล็ก สำหรับทดสอบ
                switch ($userMessage) {
                    case "p":
                        // เรียกดูข้อมูลโพรไฟล์ของ Line user โดยส่งค่า userID ของผู้ใช้ LINE ไปดึงข้อมูล
                        $response = $bot->getProfile($userID);
                        if ($response->isSucceeded()) {
                            // ดึงค่ามาแบบเป็น JSON String โดยใช้คำสั่ง getRawBody() กรณีเป้นข้อความ text
                            $textReplyMessage = $response->getRawBody(); // return string            
                            $replyData = new TextMessageBuilder($textReplyMessage);         
                            break;              
                        }
                        // กรณีไม่สามารถดึงข้อมูลได้ ให้แสดงสถานะ และข้อมูลแจ้ง ถ้าไม่ต้องการแจ้งก็ปิดส่วนนี้ไปก็ได้
                        $failMessage = json_encode($response->getHTTPStatus() . ' ' . $response->getRawBody());
                        $replyData = new TextMessageBuilder($failMessage);
                        break;              
                    case "สวัสดี":
                        // เรียกดูข้อมูลโพรไฟล์ของ Line user โดยส่งค่า userID ของผู้ใช้ LINE ไปดึงข้อมูล
                        $response = $bot->getProfile($userID);
                        if ($response->isSucceeded()) {
                            // ดึงค่าโดยแปลจาก JSON String .ให้อยู่ใรูปแบบโครงสร้าง ตัวแปร array 
                            $userData = $response->getJSONDecodedBody(); // return array     
                            // $userData['userId']
                            // $userData['displayName']
                            // $userData['pictureUrl']
                            // $userData['statusMessage']
                            $textReplyMessage = 'สวัสดีครับ คุณ '.$userData['displayName'];             
                            $replyData = new TextMessageBuilder($textReplyMessage);         
                            break;              
                        }
                        // กรณีไม่สามารถดึงข้อมูลได้ ให้แสดงสถานะ และข้อมูลแจ้ง ถ้าไม่ต้องการแจ้งก็ปิดส่วนนี้ไปก็ได้
                        $failMessage = json_encode($response->getHTTPStatus() . ' ' . $response->getRawBody());
                        $replyData = new TextMessageBuilder($failMessage);
                        break;                                                                                                                                         
					case "v":

					$picThumbnail = 'https://thumb.ibb.co/isTQZ7/prev.jpg';
                    $videoUrl = "https://www.quirksmode.org/html5/videos/big_buck_bunny.mp4";                
                    $replyData = new VideoMessageBuilder($videoUrl,$picThumbnail);

					break;
						
					case "tm":
                    $replyData = new TemplateMessageBuilder('Confirm Template',
                        new ConfirmTemplateBuilder(
                                'PO. 101120182345 wait for approve',
                                array(
                                    new MessageTemplateActionBuilder(
                                        'Approve',
                                        'Approved'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'Decline',
                                        'Declined'
                                    )
                                )
                        )
                    );
                    break;   	
						
						
						
                    default:
                       // $textReplyMessage = " คุณไม่ได้พิมพ์ ค่า ตามที่กำหนด";
                       // $replyData = new TextMessageBuilder($textReplyMessage);         
                        break;                                      
                }
                break;      
            case (preg_match('/[image|audio|video]/',$typeMessage) ? true : false) :
                $response = $bot->getMessageContent($idMessage);
                if ($response->isSucceeded()) {
                    // คำสั่ง getRawBody() ในกรณีนี้ จะได้ข้อมูลส่งกลับมาเป็น binary 
                    // เราสามารถเอาข้อมูลไปบันทึกเป็นไฟล์ได้
                    $dataBinary = $response->getRawBody(); // return binary
                    // ดึงข้อมูลประเภทของไฟล์ จาก header
                    $fileType = $response->getHeader('Content-Type');    
                    switch ($fileType){
                        case (preg_match('/^image/',$fileType) ? true : false):
                            list($typeFile,$ext) = explode("/",$fileType);
                            $ext = ($ext=='jpeg' || $ext=='jpg')?"jpg":$ext;
                            $fileNameSave = time().".".$ext;
                            break;
                        case (preg_match('/^audio/',$fileType) ? true : false):
                            list($typeFile,$ext) = explode("/",$fileType);
                            $fileNameSave = time().".".$ext;                        
                            break;
                        case (preg_match('/^video/',$fileType) ? true : false):
                            list($typeFile,$ext) = explode("/",$fileType);
                            $fileNameSave = time().".".$ext;                                
                            break;                                                      
                    }
                    $botDataFolder = 'botdata/'; // โฟลเดอร์หลักที่จะบันทึกไฟล์
                    $botDataUserFolder = $botDataFolder.$userID; // มีโฟลเดอร์ด้านในเป็น userId อีกขั้น
                    if(!file_exists($botDataUserFolder)) { // ตรวจสอบถ้ายังไม่มีให้สร้างโฟลเดอร์ userId
                        mkdir($botDataUserFolder, 0777, true);
                    }   
                    // กำหนด path ของไฟล์ที่จะบันทึก
                    $fileFullSavePath = $botDataUserFolder.'/'.$fileNameSave;
                    file_put_contents($fileFullSavePath,$dataBinary); // ทำการบันทึกไฟล์
                    //$textReplyMessage = "บันทึกไฟล์เรียบร้อยแล้ว $fileNameSave";
                    $textReplyMessage = "บันทึกไฟล์เรียบร้อยแล้ว $fileFullSavePath";
                    $replyData = new TextMessageBuilder($textReplyMessage);
                 
                    //$replyData = new TextMessageBuilder($response);
                    
                 
                    //add
                    //$content1 = file_get_contents($fileNameSave);
                    //$replyData = new MultiMessageBuilder();
                    //$replyData->add( new TextMessageBuilder($textReplyMessage))
                    //          ->add( new TextMessageBuilder($content1));
 

                    break;
                }
                $failMessage = json_encode($idMessage.' '.$response->getHTTPStatus() . ' ' . $response->getRawBody());
                $replyData = new TextMessageBuilder($failMessage);  
                break;                                                      
            default:
                $textReplyMessage = json_encode($events);
                $replyData = new TextMessageBuilder($textReplyMessage);         
                break;  
        }
    }
}
$response = $bot->replyMessage($replyToken,$replyData);
if ($response->isSucceeded()) {
    echo 'Succeeded!';
    return;
}



 
// Failed
echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
?>
