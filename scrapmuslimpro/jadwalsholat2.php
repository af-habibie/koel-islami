<?php 

$html = file_get_contents('https://www.muslimpro.com/id/Waktu-sholat-Bekasi-Indonesia-1649378');

$dom = new DOMDocument;

@$dom->loadHTML($html);

$links = $dom->getElementsByTagName('img');

foreach ($links as $link){

echo "<pre>";
print_r($link->getAttribute('src'));
echo "</pre>";
}