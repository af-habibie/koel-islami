<?php 

function httpRequest($url){
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($c);
    curl_close($c);
    return $result;
}

$source = httpRequest('https://www.muslimpro.com/id/Waktu-sholat-Bekasi-Indonesia-1649378');

$tagJudulOpen = explode('<h2 class="page-title pt-2 pt-md-4">', $source);
$tagJudulClose = explode('</h2>', $tagJudulOpen[1]);

$judulSetSpace = str_replace('di', 'di ', $tagJudulClose[0]);
$judul = str_replace('<b>', "", $judulSetSpace);

echo $judul;