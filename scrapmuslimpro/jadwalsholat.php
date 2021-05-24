<?php

$url = 'https://www.muslimpro.com/locate?country_code=ID&country_name=Indonesia&city_name=South%20Jakarta&coordinates=-6.2614927,106.8105998';

    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($c);
    curl_close($c);

$jadwalAdzanOpen = explode('<p class="prayers">Fajr</p>', $result);
$jadwalAdzanClose = explode('</p>', $jadwalAdzanOpen[1]);

echo "<pre>";
print_r($jadwalAdzanClose);
echo "</pre>";
?>

<!doctype html>
<html lang="en">
    <head> 
        <meta charset="utf-8">
        