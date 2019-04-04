<?php
// parameters

$hubVerifyToken = 'verify-token-chat';
$accessToken =   "EAAEh0ZB48ZAKgBAMfu5MzZAGZBJXIw1u0eRMoJNBKRL5DZCeaqV1ZAYTXocqYMym4bhqA6DLjNpBSJO3ZCiinclzkjeYjFa6fIC3EA3te03lTBAjWuHZAYAH9EYKfFHBzZAKgCBNXzOu2ddWnTUDmQ9UZCyCAKZCz58Auow4Gnkug6rzQZDZD";

// check token at setup
if ($_REQUEST['hub_verify_token'] == $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}

// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);

$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$payload =  $input['entry'][0]['messaging'][0]['postback']['payload'];
$response = null;

//set Message
$answer = '';
$response = [
    'recipient' => [ 'id' => $senderId ],
    'message' => ''
];
if($messageText == "hi" || $messageText == "Hi") {
    $answer = "Hello";
    $response['message'] = ['text'=>$answer];
  //send message to facebook bot

}
if($messageText == "blog"){
     $answer = ["attachment"=>[
      "type"=>"template",
      "payload"=>[
        "template_type"=>"button",
        "text"=>"What do you want to do next?",
        "buttons"=>[
          [
            "type"=>"web_url",
            "url"=>"https://petersapparel.parseapp.com",
            "title"=>"Show Website"
          ],
          [
            "type"=>"postback",
            "title"=>"Start Chatting",
            "payload"=>"USER_DEFINED_PAYLOAD"
          ]
        ]
      ]
    ]];
    $response['message'] = $answer;
}

if (strpos($messageText, 'wea') !== false) {
  $answer = '';
  $option = explode(" ", $messageText); // Разбивает строку с помощью разделителя $option[1]-Киев,$option[2] = число 3
  require ('phpQuery.php');  // библиотека
  $url = 'https://ua.sinoptik.ua/погода-'.$option[1];


  $html = file_get_contents($url);

  //$html=curl_content('https://ua.sinoptik.ua/погода-'.$option[1]);// парсим все 10 дней 
  phpQuery::newDocument($html);
 
  $content = phpQuery::newDocument( $html );
  $content = pq( $content )->find( '#blockDays > .tabs' )->find('.main');
  $xbodycontent = pq('#blockDays > .tabs')->find('.main');
  $i=0;
  foreach ($xbodycontent as $res) {
	    $pqres=pq($res);
	    $day = $pqres->find('.day-link')->text();//день
	    $date = $pqres->find('.date')->text();//дата
	    $icon = $pqres->find('.weatherIco')->attr('title');//надпись на картинке
	    $iconimg = $pqres->find('.weatherImg')->html();//надпись на картинке
	    $temperature = $pqres->find('.temperature')->text();// Температура
	    //проверяем что а погода чтобы определить какой смайлик отправить для визуализации
	    $emoji = '';
	  if ($icon == 'Сплошная облачность, небольшой снег') {
	    $emoji = hex2bin('F09F8CA809'); //hex2bin('f09f9880') //представление смайла https://apps.timwhitlock.info/emoji/tables/unicode
	  }
	  if ($icon == 'Сплошная облачность, сильный снег') {
	    $emoji = hex2bin('F09F8CA809');
	  }
	   if ($icon == 'Сплошная облачность, снег') {
	    $emoji = hex2bin('E29B84');
	  }
	  if ($icon == 'Сплошная облачность') {
	    $emoji = hex2bin('E29881');
	  }
	  if ($icon == 'Облачно с прояснениями, мокрый снег') {
	    $emoji =hex2bin('F09F8CA609');
	  }
	  if ($icon == 'Облачно с прояснениями') {
	    $emoji =hex2bin('F09F8CA409');
	  }
	  if ($icon == 'Облачно с прояснениями, небольшой снег') {
	    $emoji =hex2bin('E29D8409');
	  }
	 // оправляем в канал результат
	  $res = $day.' '.$temperature.chr(10).' '.$emoji.' '.$icon.chr(10).chr(10);
	  $answer .= $res; 
	  
	  $i++;
	  //requestToTelegram($content);
	  if($i == 3){
	     break;
	  }
	}

  $response['message'] = ['text'=>$answer];
}
if($payload){
  $response['message'] = ['text'=>$payload];
}



$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
if(!empty($input)){
$result = curl_exec($ch);
}
curl_close($ch);


function sendMsg(){
	$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	if(!empty($input)){
	$result = curl_exec($ch);
	}
	curl_close($ch);
}
?>
