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
?>
