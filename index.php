<?php
require 'phpQuery.php';


function print_arr($arr){
    echo '<pre>' . print_r($arr, true) . '</pre>';
}


function connectToDb(){
    $username = 'admin';
    $password = 'test1234!';
    $host = 'localhost';
    $database = 'qdduicyx_tinbank';
    $link = mysqli_connect($host, $username, $password, $database);
    if(!$link){
        return false;
    }
    return $link;
}


function getUrlContent($url){
    $options = array(
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true
    );
    $chanel = curl_init($url);
    curl_setopt_array($chanel, $options);
    return curl_exec($chanel);

}


function getRegionsDataFromDb(){
    $link = connectToDb();
    if ($link === false){
        return -1;
    }
    $sql = 'SELECT `id`, `name` FROM regions';
    $result = mysqli_query($link, $sql);

    mysqli_close($link);
    return $result->fetch_all(MYSQLI_ASSOC);
}


function insertBranches($branches){
    $link = connectToDb();
    $sql = "INSERT INTO `branches`(`regnum`, `city`, `rid`, `name`, `adr`, `longitude`, `latitude`, `phone`, `phone_obn_val`, `emails`, `addinfo`, `pay`, `resource`, `chek`, `URL`, `URL_city`, `dop_inf`, `latcity`, `point`, `google_oid`, `google_site`, `google_site_new`, `google_full_name`, `google_photo`, `google_code`, `google_source_url`, `google_hash`) VALUES";
    for($i = 0; $i < count($branches); ++$i){
        $sql .= "('" . $branches[$i]['regnum'] . "', '" . $branches[$i]['city'] . "', '" . $branches[$i]['rid'] . "', '" . $branches[$i]['name'] . "', '" . $branches[$i]['adr'] . "', '" . $branches[$i]['longitude'] . "', '" . $branches[$i]['latitude'] . "', '" . $branches[$i]['phone'] . "', '" . $branches[$i]['phone_obn_val'] . "', '" . $branches[$i]['emails'] . "', '" . $branches[$i]['addinfo'] . "', '" . $branches[$i]['pay'] . "', '" . $branches[$i]['resource'] . "', '" . $branches[$i]['chek'] . "', '" . $branches[$i]['URL'] . "', '" . $branches[$i]['URL_city'] . "', '" . $branches[$i]['dop_inf'] . "', '" . $branches[$i]['latcity'] . "', '" . $branches[$i]['point'] . "', '" . $branches[$i]['google_oid'] . "', '" . $branches[$i]['google_site'] . "', '" . $branches[$i]['google_site_new'] . "', '" . $branches[$i]['google_full_name'] . "', '" . $branches[$i]['google_photo'] . "', '" . $branches[$i]['google_code'] . "', '" . $branches[$i]['google_source_url'] . "', '" . $branches[$i]['google_hash'] ."')" . (($i === count($branches) - 1) ? ';':', ');
    }
    $result = mysqli_query($link, $sql);
    if($result === false){
        print_arr('There was an error in branches. Mysqli error: '. mysqli_error($link));
    }
    else{
        print_arr('Branches Added');
    }
    mysqli_close($link);
}


function insertAtms($atm){
    $link = connectToDb();
    $sql = "INSERT INTO `bankomats`(`regnum`, `city`, `rid`, `name`, `adr`, `longitude`, `latitude`, `phone`, `emails`, `pay`, `work_full_time`, `org_pay`, `send_pay`, `print_pas`, `addinfo`, `resource`, `chek`, `URL`, `URL_city`, `dop_inf`, `latcity`, `point`, `google_oid`, `google_site`, `google_site_new`, `google_full_name`, `google_photo`, `google_code`, `google_source_url`, `google_hash`, `nal_eur`, `max_eur`, `nal_usd`, `max_usd`) VALUES";
    for($i = 0; $i < count($atm); ++$i){
        $sql .= "('" . $atm[$i]['regnum'] . "', '" . $atm[$i]['city'] . "', '" . $atm[$i]['rid'] . "', '" . $atm[$i]['name'] . "', '" . $atm[$i]['adr'] . "', '" . $atm[$i]['longitude'] . "', '" . $atm[$i]['latitude'] . "', '" . $atm[$i]['phone'] . "', '" . $atm[$i]['emails'] . "', '" . $atm[$i]['pay'] . "', '" . $atm[$i]['work_full_time'] . "', '" . $atm[$i]['org_pay'] . "', '" . $atm[$i]['send_pay'] . "', '" . $atm[$i]['print_pas'] . "', '" . $atm[$i]['addinfo'] . "', '" . $atm[$i]['resource'] . "', '" . $atm[$i]['chek'] . "', '" . $atm[$i]['URL'] . "', '" . $atm[$i]['URL_city'] . "', '" . $atm[$i]['dop_inf'] . "', '" . $atm[$i]['latcity'] . "', '" . $atm[$i]['point'] . "', '" . $atm[$i]['google_oid'] . "', '" . $atm[$i]['google_site'] . "', '" . $atm[$i]['google_site_new'] . "', '" . $atm[$i]['google_full_name'] . "', '" . $atm[$i]['google_photo'] . "', '" . $atm[$i]['google_code'] . "', '" . $atm[$i]['google_source_url'] . "', '" . $atm[$i]['google_hash'] . "', '" . $atm[$i]['nal_eur'] . "', '" . $atm[$i]['max_eur'] . "', '" . $atm[$i]['nal_usd'] . "', '" . $atm[$i]['max_usd'] . "')" . ($i === count($atm) - 1) ? ';': ', ';
    }
    $result = mysqli_query($link, $sql);
    if($result === false){
        print_arr('There was an error in atms. Mysqli error: '. mysqli_error($link));
    }
    else{
        print_arr('Atms Added');
    }
    mysqli_close($link);

}



// Заходим на сайт и узнаем id всех регионов, которые используются на сайте

$url = "https://www.ocb.com.vn/vi/mang-luoi-ocb.html";
$result = getUrlContent($url);

$doc = phpQuery::newDocument($result);
$doc->find('#agency-province option[value="0"]')->remove();
$listOfRegions = $doc->find('#agency-province option');

$selectOptions = array();


foreach ($listOfRegions as $region){
    $region = pq($region);

    $selectId = $region->attr('value');
   // echo $selectId;

   $name = $region->text();
   $count = 1;
   $name = str_replace('.', '. ', $name, $count);
   $selectOptions[] = array(
       'id' => $selectId,
       'name' => $name
   );
}
phpQuery::unloadDocuments($doc);

// Достаем rid з базы данных и добавляем в массив $selectOptions
$regions = getRegionsDataFromDb();
if ($regions === false)
{
    echo -1;
    exit(0);
}
for($i = 0; $i < count($selectOptions); ++$i){
    for($j = 0; $j < count($regions); ++$j){
        if(!strcasecmp($selectOptions[$i]['name'], $regions[$j]['name'])){

            $selectOptions[$i]['rid'] = $regions[$j]['id'];
        }
    }
}
/*
Cам парсинг. Создаем массивы tm branches парсим с их "api".
Т.к. их апи может выдавать только по 20 элементов использую доп. переменные startPos и endPos,
чтобы достать всю информацию
Удаляю из названий нумерецаию их и достаю чистый режим работы без лишних символов.
С помощьб api от google получаю geocode.
Заполняю массивы atm и branches на основе присутствия слова atm в названии. Сделал так, потому что их поиск немного сломан.

*/
$atm = array();
$branches = array();
$regnum = "0061";
$apiKey = "AIzaSyByGvQp_-zNDtOazqD7Hw5DT4XRQ5UzrvE";
for ($i = 0; $i < count($selectOptions); ++$i){
    $startPos = 0;
    $endPos = $startPos + 20;
    $id = $selectOptions[$i]['id'];
    $rid = $selectOptions[$i]['rid'];
    $url = "https://www.ocb.com.vn/vi/readmore?url=%2Fvi%2Freadmore&idstring=2-moremapatm-${startPos}&count_row=${endPos}&provinceid=${id}&districtid=0";
    $result = getUrlContent($url);

    while(!empty($result)){
        $doc = phpQuery::newDocument($result);
        $lists = $doc->find('li');
        foreach ($lists as $li){
            $li = pq($li);

            $name = $li->find('p.item-network')->text();
            $name = substr($name, strpos($name, '.') + 2);

            $address = $li->find('p.view-address')->text();
            $geoAddress = urlencode($address);
            $geocode_url = "https://maps.googleapis.com/maps/api/geocode/json?address=${geoAddress}&key=${apiKey}";
            $geocode = json_decode(getUrlContent($geocode_url), true);
            $geometry = $geocode['results'][0]['geometry'];
            $latitude = $geometry['location']['lat'];
            $longitude = $geometry['location']['lng'];
            $phone = $li->find('p.view-phone')->text();

            $time = $li->find('p.view-time')->text();
            $time = substr($time, strpos($time, ':') + 2);
            if (stripos($name, 'atm') !== false){
                $atm[] = array(
                    'regnum'            => $regnum,
                    'name'              => $name,
                    'adr'               => $address,
                    'phone'             => $phone,
                    'pay'               => $time,
                    'rid'               => $rid,
                    'longitude'         => $longitude,
                    'latitude'          => $latitude,
                    'emails'            => '',
                    'work_full_time'    => 0,
                    'org_pay'           => 0,
                    'send_pay'          => 0,
                    'print_pas'         => 0,
                    'addinfo'           => NULL,
                    'city'              => '',
                    'resource'          => '',
                    'chek'              => 0,
                    'URL'               => NULL,
                    'URL_city'          => '',
                    'dop_inf'           => NULL,
                    'latcity'           => '',
                    'point'             => '',
                    'google_oid'        => '',
                    'google_site'       => '',
                    'google_site_new'   => '',
                    'google_full_name'  => '',
                    'google_photo'      => '',
                    'google_code'       => '',
                    'google_source_url' => '',
                    'google_hash'       => '',
                    'nal_eur'           => 0,
                    'max_eur'           => '',
                    'nal_usd'           => 0,
                    'max_usd'           => ''
                );
            }
            else{
                $branches[] = array(
                    'regnum'            => $regnum,
                    'name'              => $name,
                    'adr'               => $address,
                    'phone'             => $phone,
                    'pay'               => $time,
                    'rid'               => $rid,
                    'longitude'         => $longitude,
                    'latitude'          => $latitude,
                    'phone_obn_val'     => '',
                    'emails'            => '',
                    'addinfo'           => NULL,
                    'city'              => '',
                    'resource'          => '',
                    'chek'              => 0,
                    'URL'               => NULL,
                    'URL_city'          => '',
                    'dop_inf'           => NULL,
                    'latcity'           => '',
                    'point'             => '',
                    'google_oid'        => '',
                    'google_site'       => '',
                    'google_site_new'   => '',
                    'google_full_name'  => '',
                    'google_photo'      => '',
                    'google_code'       => '',
                    'google_source_url' => '',
                    'google_hash'       => '',
                );
            }

        }

        $startPos = $endPos;
        $endPos += 20;
        $url = "https://www.ocb.com.vn/vi/readmore?url=%2Fvi%2Freadmore&idstring=2-moremapatm-${startPos}&count_row=${endPos}&provinceid=${id}&districtid=0";
        $result = getUrlContent($url);
        phpQuery::unloadDocuments($doc);

    }

}

//Импорт в бд
insertAtms($atm);
insertBranches($branches);



