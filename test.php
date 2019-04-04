
<?php
$messageText = $_GET['q'];
var_dump(strpos($messageText, 'wea') !== false);
if (strpos($messageText, 'wea') !== false) {
  $option = explode(" ", $messageText); // Разбивает строку с помощью разделителя $option[1]-Киев,$option[2] = число 3
  require ('phpQuery.php');  // библиотека
  $url = 'https://sinoptik.ua/погода-'.$option[1].'/10-дней';

  $html = file_get_contents($url);

  //$html=curl_content('https://sinoptik.ua/погода-'.$option[1].'/10-дней');// парсим все 10 дней 
  phpQuery::newDocument($html);
 
  $content = phpQuery::newDocument( $html );
	
 // $content = pq( $content )->find( '#blockDays > .tabs' )->find('.main');
  $xbodycontent =  pq( $content )->find( '#blockDays > .tabs' )->find('.main');
	var_dump( count($xbodycontent));
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
  $response['message'] = ['text'=>$day.$date.chr(10).$temperature.chr(10).$emoji.$icon];
  var_dump($response);
  $i++;
	  //requestToTelegram($content);
	if($i == 2){
		break;
	}
}

}

?>
