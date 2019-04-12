<?php
// parameters

$hubVerifyToken = 'weather-token';
$accessToken =   "EAAGKCpwmIrEBAOokeeN9xwFmKOOMPkomCaUtavxh4Pu3UHZAn1VuwTtx8vKaget2SE7Kt2Go5QNKCQ9Azp3BPiqtBzkRePhvjrj9vIUsMCnpBP6r1JoZCjjSz10Tiv76p4Jp7MKZAoACX5miB12nV69gVzNThXZCkTEz9J7csAZDZD";

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
if($messageText == "hi" || $messageText == "Hi" || $messageText == "Hello") {
    $answer = "Hello";
    $response['message'] = ['text'=>$answer];
  //send message to facebook bot

}else if($messageText == "blog"){
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
}else if (strpos($messageText, 'wea') !== false || strpos($messageText, 'Wea') !== false || strpos($messageText, 'огода') !== false) {
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
	    //проверяем что а погода чтобы определить какой смайлик отправить для визуализаци
	 // оправляем в канал результат
	  $res = $day.' '.$temperature.chr(10).$icon.chr(10).chr(10);
	  $answer .= $res; 
	  
	  $i++;
	  //requestToTelegram($content);
	  if($i == 3){
	     break;
	  }
	}

  $response['message'] = ['text'=>$answer];
}else if($payload){
  $response['message'] = ['text'=>$payload];
}else if(strpos($messageText, 'location') !== false ){

	$ch = curl_init('https://graph.facebook.com/v3.2/'.$senderId.'/');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	$response['message'] = ['text'=>json_encode($result)];
}else if($messageText == 'b'){
	$ch = curl_init("https://graph.facebook.com/$senderId?fields=id,name&access_token=$accessToken");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	$response['message'] = ['text'=>json_encode($result).'https://graph.facebook.com/'.$chat_id.'?fields=id&access_token='.$accessToken];
}else{
  $response['message'] = ['text'=>"Sorry, I don't understand you"];
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
