<?php
require 'vendor/autoload.php';
use Goutte\Client;
$client = new Client();

$crawler = $client->request('GET', 'http://eoi.afa.gov.tw/');
$form = $crawler->selectButton('查詢')->form();

$client->setHeader('User-Agent', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:59.0) Gecko/20100101 Firefox/59.0');
$client->setHeader('Host', 'eoi.afa.gov.tw');
$client->setHeader('Referer', 'http://eoi.afa.gov.tw');

$cities = array(
  '03' => '桃園市',
  '21' => '台北市',
'01' => '新北市',
'02' => '宜蘭縣',
'03' => '桃園市',
'04' => '新竹縣',
'24' => '新竹市',
'05' => '苗栗縣',
'06' => '台中市',
'07' => '彰化縣',
'08' => '南投縣',
'09' => '雲林縣',
'10' => '嘉義縣',
'25' => '嘉義市',
'11' => '台南市',
'12' => '高雄市',
'13' => '屏東縣',
'14' => '台東縣',
'15' => '花蓮縣',
'16' => '澎湖縣',
'17' => '基隆市',
'22' => '金門縣',
'23' => '連江縣',
);

// http://eoi.afa.gov.tw/getobjinfo.php?objidcal=123803029345872
$fh = fopen(__DIR__ . '/data.csv', 'w');
$result = array();
$headerDone = false;
foreach($cities AS $cityId => $city) {
  error_log($city);
  $crawler = $client->submit($form, array('QAreaID' => $cityId));
  $c = $client->getResponse()->getContent();
  $parts = explode("class='CallObjInfoClass' data-id=", $c);
  array_shift($parts);
  foreach($parts AS $part) {
    $id = substr($part, 1, 15);
    $d = file_get_contents('http://eoi.afa.gov.tw/getobjinfo.php?objidcal=' . $id);
    $d = substr($d, strpos($d, '['));
    $data = json_decode($d, true);
    if(!isset($data[0])) {
      continue;
    }
    if(!isset($result[$id])) {
      $result[$id] = true;
      if(false === $headerDone) {
        $headerDone = true;
        fputcsv($fh, array_keys($data[0]));
      }
      fputcsv($fh, $data[0]);
    }
  }
}
