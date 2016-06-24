<?php



header("Content-type: text/html; charset=utf-8");

//session_start();

set_time_limit(0);

require("classes.php");
require_once("modules/simple_html_dom.php");
//require_once("../Db.class.php");
global $db, $prs, $login, $pass, $count_page, $date_from, $date_to, $ch;
global $kv_arr,$dom_arr,$bussines_arr,$ychastok_arr,$garazh_arr;

$db = new Db();
//$prs = new Parser(false);

$login = 'dp-avangard@rambler.ru';
$pass = 'yaroslav';
$count_page = 25;

$date_from = date('Y-m-d');

//$date_from = '2016-04-01';
$date_to = date('Y-m-d');
//$date_to = '2016-04-01';
//$ch = curl_init();

$kv_arr = array("комната","квартира","гостинка","квартира посуточно","квартира в новострое");
$dom_arr = array("часть дома","дача","дом","дом (новострой)","дом посуточно","недострой","дача (новострой)","комната в доме");
$bussines_arr = array("магазин"," помещение"," офис"," торговая площадь"," склад","офис посуточно","объект автосервиса","объект красоты","объект здоровья","объект питания","объект производства","объект мероприятий","объект промышленности","другое","маф","салон","кафе","здание","объект спорта","объект отдыха","бар ресторан","зал");
$ychastok_arr = array("участок под жилье","сельхоз участок","дачный участок","коммерческий участок","участок");
$garazh_arr = array("гараж", "паркинг", "парковка");

sale_baza_broker();
rent_baza_broker();
snimy_baza_broker();
kypliy_baza_broker();

function login($ch,$url,$login,$pass){
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    // откуда пришли на эту страницу
    curl_setopt($ch, CURLOPT_REFERER, $url);
    // cURL будет выводить подробные сообщения о всех производимых действиях
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,"login=".$login."&password=".$pass);
    //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //Cохранять полученные COOKIE в файл
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie1.txt");
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie1.txt");
    $result=curl_exec($ch);

    return $result;
}

// чтение страницы после авторизации
function read($ch,$url){
    //$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // откуда пришли на эту страницу
    curl_setopt($ch, CURLOPT_REFERER, $url);
    //запрещаем делать запрос с помощью POST и соответственно разрешаем с помощью GET
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie1.txt");
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie1.txt");

    //отсылаем серверу COOKIE полученные от него при авторизации
    curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookie.txt');
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4");

    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

///////////////////////////////////////////////////////////////////////////// SALE
function sale_baza_broker()
{
    global $db, $prs, $login, $pass, $count_page, $date_from, $date_to, $ch;
    global $kv_arr,$dom_arr,$bussines_arr,$ychastok_arr,$garazh_arr;
    $type_n = "sale";
    $page = 1;

    $ch = curl_init();
    $ret = login($ch,"http://ua.baza-broker.com/login",'dp-avangard@rambler.ru','yaroslav');
    $ret = read($ch,"http://baza-broker.com/index.php?do=baza&type=" . $type_n . "&page=" . $page . "&region=3&per_page=" . $count_page . "&dt=" . $date_from . "%2C" . $date_to . "&sub_type=flat&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30&view=list");
    $html = new simple_html_dom();
    $html = str_get_html($ret);
    $col = $html->find('.objects_list_title_site_selected',0)->plaintext;
    $pages = $col/$count_page;
    $pages = round($pages)+1;

    for ($i=1;$i<=$pages;$i++) {
        $ch = curl_init();
        $ret = login($ch, "http://ua.baza-broker.com/login", 'dp-avangard@rambler.ru', 'yaroslav');
        $ret = read($ch, "http://baza-broker.com/index.php?do=baza&type=" . $type_n . "&page=" . $i . "&region=3&per_page=" . $count_page . "&dt=" . $date_from . "%2C" . $date_to . "&sub_type=flat&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30&view=list");
        $html = new simple_html_dom();
        $html = str_get_html($ret);
        $tables = $html->find('div.offer_container');
        $rowData = array();
        $data_arr = array();
        $j = 0;

        foreach ($tables as $td) {


            $title = $td->find('div.objects_item_title', 0)->plaintext;

            $id = $td->find('div.objects_item_title', 0)->parent()->parent()->id;
            $id = explode("_", $id);
            $id = $id[1];
            $addr = $td->find('div.objects_item_addr');


            $city = "";
            $adraion = "";
            $mraion = "";
            $street = "";
            $house = "";
            $r = 0;

            $kichen = 0;
            $rooms = 0;
            $floor = 0;
            $floors = 0;
            $area = 0;
            $living_area = 0;
            $city = "---";
            $adraion = "---";
            $mraion = "---";
            $street = "---";
            $house = "---";
            foreach ($addr as $num) {
                $n = $num->find('a', 0)->href;
                $n = explode("list&", $n);
                $n = $n[1];

                if (stripos($n, "egion")) {
                    $city = $num->find('a', 0)->parent()->plaintext;

                } elseif (stripos($n, "istricts[]")) {
                    $adraion = $num->find('a', 0)->parent()->plaintext;
                } elseif (stripos($n, "ities[]")) {
                    $mraion = $num->find('a', 0)->parent()->plaintext;
                } elseif (stripos($n, "treets[]")) {
                    $street = $num->find('a', 0)->parent()->plaintext;
                } elseif (stripos($n, "ouse")) {
                    $house = $num->find('a', 0)->parent()->plaintext;
                }
            }
            $obj_4 = $td->find('div.objects_item_info_col_4', 0)->find('tr');

            foreach ($obj_4 as $num) {
                $n = $num->plaintext;

                if (stripos($n, "омнат:")) {
                    $rooms = explode("Комнат:", $n);
                    $rooms = $rooms[1];

                } elseif (stripos($n, "тажность")) {
                    $floors = explode("Этажность", $n);
                    $floors = $floors[1];
                    $floors = explode("/", $floors);
                    $floor = $floors[0];
                    $floors = $floors[1];
                } elseif (stripos($n, "бщая:")) {
                    $area = explode("&nbsp;", $n);
                    $area = $area[1];
                } elseif (stripos($n, "илая:")) {
                    $living_area = explode("&nbsp;", $n);
                    $living_area = $living_area[1];

                } elseif (stripos($n, "ухня:")) {
                    $kichen = explode("&nbsp;", $n);
                    $kichen = $kichen[1];
                }
            }

            $area_json = "$area / $living_area / $kichen";

            $type = $td->find('div.objects_item_info_col_w', 1)->plaintext;

            if (stripos($type, 'договорная')) {
                $type = explode("цена договорная", $type);
                $price = "цена договорная";
                $type = $type[1];
            } else {
                $type_cost = explode("грн", $type);
                $type = explode(",", $type_cost[1]);
                $type = $type[0];
                $cost = explode("$", $type_cost[0]);
                $price = $cost[0];
                $price = str_replace(" ", "", $price);
            }


            $table = $td->find('table.objects_item_props', 0);
            foreach ($table->find('tr') as $tr) {
                $obj = explode(":", $tr);
                //var_dump($obj);
            }

            $phone = $td->find('div.objects_item_phones', 0)->plaintext;
            $phone = explode(":", $phone);
            $phone = $phone[1];
            $metka = $td->find('span.objects_item_dt_added', 0)->plaintext;
            $date_obj = $td->find('span.objects_item_dt_added', 1)->plaintext;

            $date = date("Y-m-d");
            $d = new DateTime($date);


            if (stripos($date_obj, 'сегодня') !== false) {

                $date_obj = str_replace("сегодня", $d->format("d-m-Y"), $date_obj);

            } elseif (stripos($date_obj, 'вчера') !== false) {
                $d->modify("-1 day");
                $date_obj = str_replace("вчера", $d->format("d-m-Y"), $date_obj);

            }

            $link = $td->find('a.objects_item_info_col_card_link', 0)->href;
            $link = "https://ua.baza-broker.com/" . $link;


            $text = $td->find('div.objects_item_info_col_comment_text', 0)->plaintext;
            //echo "<p>".$text."</p>";
            $region = "Днепропетровская обл.";
            $operation = 1;

            $marketSegment = $td->find('div.objects_item_info_col_2 div', 0)->find("div", 1)->plaintext;
            $estateType = trim($marketSegment);
            if (in_array($estateType, $kv_arr)) $marketSegment = 1;
            if (in_array($estateType, $dom_arr)) $marketSegment = 2;
            if (in_array($estateType, $bussines_arr)) $marketSegment = 3;
            if (in_array($estateType, $ychastok_arr)) $marketSegment = 4;
            if (in_array($estateType, $garazh_arr)) $marketSegment = 5;

            if (stripos($estateType,'осуточно')) {
                $operation = 3;
                $estateType = str_replace("посуточно", "" ,$estateType);
                $estateType = trim($estateType);
            };

            //echo    "$estateType - $marketSegment";
            // echo $html;


            $photo = $td->find('.objects_item_images_w > a');
            $photo_arr = '';
            $p = 0;
            if ($photo) {
                foreach ($photo as $a) {
                    if ($p == 0) {
                        $photo_arr .= $a->href;
                    } else {
                        $photo_arr .= ":::" . $a->href;
                    }
                    $p++;
                }

            }


            $flag = 0;
            if (isset($adraion){
                if ((trim($adraion) == "АНД") || (trim($adraion) == 'Амур-Нижнеднепровский')) $adraion = "Амур-Нижнеднепровский";

            }

            $db_arr = array(
                'id' => strval($id) + 0,
                'frm' => 4,
                'region' => $region,
                'city' => str_replace(" ", "", $city),
                'adistrict' => $mraion,
                'district' => $adraion,
                'street' => $street,
                'rooms' => strval(str_replace("&nbsp;", "", $rooms)),
                'floor' => strval(str_replace("&nbsp;", "", $floor)),
                'max_floor' => strval(str_replace("&nbsp;", "", $floors)),
                'area' => strval(str_replace("&nbsp;", "", $area)),
                'area_json' => $area_json,
                'operation' => $operation,
                'marketSegment' => $marketSegment,
                'estateType' => $estateType,
                'adDate' => strtotime($date_obj),
                'phones' => $phone,
                'price' => strval(str_replace("&nbsp;", "", $price)),
                'text' => $text,
                'url' => $link,
                'images' => $photo_arr,
                'flags' => $flag,
            );
            $sql = "SELECT COUNT(*) as count from parsed WHERE id='$id' AND price='$price' AND phones = '$phone'";


            $sql_controll = $db->query($sql);


            //var_dump($db_arr);

            if ($sql_controll[0]['count'] != 0) {

                 return Parser::Log('Dublicate info!');
            } else {


                $db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,floor,max_floor,area,area_json,operation,marketSegment,estateType,adDate,phones,price,text,url,images,flags)
                            VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:floor,:max_floor,:area,:area_json,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:images,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                    $db_arr);

            }


        }
    }
}

///////////////////////////////////////////////////////////////////////////// Sale end
///////////////////////////////////////////////////////////////////////////// Rent
function rent_baza_broker()
{
    global $db, $prs, $login, $pass, $count_page, $date_from, $date_to, $ch;
    global $kv_arr,$dom_arr,$bussines_arr,$ychastok_arr,$garazh_arr;
    $type_n = "rent";
    $page = 1;
    $ch = curl_init();
    $ret = login($ch,"http://ua.baza-broker.com/login",'dp-avangard@rambler.ru','yaroslav');
    $ret = read($ch,"http://baza-broker.com/index.php?do=baza&type=" . $type_n . "&page=" . $page . "&region=3&per_page=" . $count_page . "&dt=" . $date_from . "%2C" . $date_to . "&sub_type=flat&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30&view=list");
    $html = new simple_html_dom();
    $html = str_get_html($ret);
    $col = $html->find('.objects_list_title_site_selected',0)->plaintext;
    $pages = $col/$count_page;
    $pages = round($pages)+1;


    for ($i=1;$i<=$pages;$i++) {
        $ch = curl_init();
        $ret = login($ch, "http://ua.baza-broker.com/login", 'dp-avangard@rambler.ru', 'yaroslav');
        $ret = read($ch, "http://baza-broker.com/index.php?do=baza&type=" . $type_n . "&page=" . $i . "&region=3&per_page=" . $count_page . "&dt=" . $date_from . "%2C" . $date_to . "&sub_type=flat&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30&view=list");
        $html = new simple_html_dom();
        $html = str_get_html($ret);

        $tables = $html->find('div.offer_container');
        $rowData = array();
        $data_arr = array();
        $j = 0;

        foreach ($tables as $td) {


            $title = $td->find('div.objects_item_title', 0)->plaintext;

            $id = $td->find('div.objects_item_title', 0)->parent()->parent()->id;
            $id = explode("_", $id);
            $id = $id[1];
            $addr = $td->find('div.objects_item_addr');


            $city = "";
            $adraion = "";
            $mraion = "";
            $street = "";
            $house = "";
            $r = 0;

            $kichen = 0;
            $rooms = 0;
            $floor = 0;
            $floors = 0;
            $area = 0;
            $living_area = 0;
            $city = "---";
            $adraion = "---";
            $mraion = "---";
            $street = "---";
            $house = "---";
            foreach ($addr as $num) {
                $n = $num->find('a', 0)->href;
                $n = explode("list&", $n);
                $n = $n[1];

                if (stripos($n, "egion")) {
                    $city = $num->find('a', 0)->parent()->plaintext;

                } elseif (stripos($n, "istricts[]")) {
                    $adraion = $num->find('a', 0)->parent()->plaintext;
                } elseif (stripos($n, "ities[]")) {
                    $mraion = $num->find('a', 0)->parent()->plaintext;
                } elseif (stripos($n, "treets[]")) {
                    $street = $num->find('a', 0)->parent()->plaintext;
                } elseif (stripos($n, "ouse")) {
                    $house = $num->find('a', 0)->parent()->plaintext;
                }
            }
            $obj_4 = $td->find('div.objects_item_info_col_4', 0)->find('tr');

            foreach ($obj_4 as $num) {
                $n = $num->plaintext;

                if (stripos($n, "омнат:")) {
                    $rooms = explode("Комнат:", $n);
                    $rooms = $rooms[1];

                } elseif (stripos($n, "тажность")) {
                    $floors = explode("Этажность", $n);
                    $floors = $floors[1];
                    $floors = explode("/", $floors);
                    $floor = $floors[0];
                    $floors = $floors[1];
                } elseif (stripos($n, "бщая:")) {
                    $area = explode("&nbsp;", $n);
                    $area = $area[1];
                } elseif (stripos($n, "илая:")) {
                    $living_area = explode("&nbsp;", $n);
                    $living_area = $living_area[1];

                } elseif (stripos($n, "ухня:")) {
                    $kichen = explode("&nbsp;", $n);
                    $kichen = $kichen[1];
                }
            }

            $area_json = "$area / $living_area / $kichen";

            $type = $td->find('div.objects_item_info_col_w', 1)->plaintext;

            if (stripos($type, 'договорная')) {
                $type = explode("цена договорная", $type);
                $price = "цена договорная";
                $type = $type[1];
            } else {
                $type_cost = explode("грн", $type);
                $type = explode(",", $type_cost[1]);
                $type = $type[0];
                $cost = explode("$", $type_cost[0]);
                $price = $cost[0];
                $price = str_replace(" ", "", $price);
            }


            $table = $td->find('table.objects_item_props', 0);
            foreach ($table->find('tr') as $tr) {
                $obj = explode(":", $tr);
                //var_dump($obj);
            }

            $phone = $td->find('div.objects_item_phones', 0)->plaintext;
            $phone = explode(":", $phone);
            $phone = $phone[1];
            $metka = $td->find('span.objects_item_dt_added', 0)->plaintext;
            $date_obj = $td->find('span.objects_item_dt_added', 1)->plaintext;

            $date = date("Y-m-d");
            $d = new DateTime($date);


            if (stripos($date_obj, 'сегодня') !== false) {

                $date_obj = str_replace("сегодня", $d->format("d-m-Y"), $date_obj);

            } elseif (stripos($date_obj, 'вчера') !== false) {
                $d->modify("-1 day");
                $date_obj = str_replace("вчера", $d->format("d-m-Y"), $date_obj);

            }

            $link = $td->find('a.objects_item_info_col_card_link', 0)->href;
            $link = "https://ua.baza-broker.com/" . $link;

            $text = $td->find('div.objects_item_info_col_comment_text', 0)->plaintext;
            //echo "<p>".$text."</p>";
            $region = "Днепропетровская обл.";
            $operation = 2;
            $marketSegment = $td->find('div.objects_item_info_col_2 div', 0)->find("div", 1)->plaintext;
            $estateType = trim($marketSegment);
            if (in_array($estateType, $kv_arr)) $marketSegment = 1;
            if (in_array($estateType, $dom_arr)) $marketSegment = 2;
            if (in_array($estateType, $bussines_arr)) $marketSegment = 3;
            if (in_array($estateType, $ychastok_arr)) $marketSegment = 4;
            if (in_array($estateType, $garazh_arr)) $marketSegment = 5;

            if (stripos($estateType,'осуточно')) {
                $operation = 3;
                $estateType = str_replace("посуточно", "" ,$estateType);
                $estateType = trim($estateType);
            };

            $photo = $td->find('.objects_item_images_w > a');
            $photo_arr = '';
            $p = 0;
            if ($photo) {
                foreach ($photo as $a) {
                    if ($p == 0) {
                        $photo_arr .= $a->href;
                    } else {
                        $photo_arr .= ":::" . $a->href;
                    }
                    $p = 1;
                }
            }


            $flag = 0;

            $db_arr = array(
                'id' => strval($id) + 0,
                'frm' => 4,
                'region' => $region,
                'city' => str_replace(" ", "", $city),
                'adistrict' => $mraion,
                'district' => $adraion,
                'street' => $street,
                'rooms' => strval(str_replace("&nbsp;", "", $rooms)),
                'floor' => strval(str_replace("&nbsp;", "", $floor)),
                'max_floor' => strval(str_replace("&nbsp;", "", $floors)),
                'area' => strval(str_replace("&nbsp;", "", $area)),
                'area_json' => $area_json,
                'operation' => $operation,
                'marketSegment' => $marketSegment,
                'estateType' => $estateType,
                'adDate' => strtotime($date_obj),
                'phones' => $phone,
                'price' => strval(str_replace("&nbsp;", "", $price)),
                'text' => $text,
                'url' => $link,
                'images' => $photo_arr,
                'flags' => $flag,
            );
            $sql = "SELECT COUNT(*) as count from parsed WHERE id='$id' AND price='$price' AND phones = '$phone'";


            $sql_controll = $db->query($sql);


            //var_dump($db_arr);

            if ($sql_controll[0]['count'] != 0) {

              return Parser::Log('Dublicate info!');
            } else {


                $db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,floor,max_floor,area,area_json,operation,marketSegment,estateType,adDate,phones,price,text,url,images,flags)
                                  VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:floor,:max_floor,:area,:area_json,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:images,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                    $db_arr);

            }


        }


       // echo $html;
    }
}
///////////////////////////////////////////////////////////////////////////// Rent end
///////////////////////////////////////////////////////////////////////////// Snimu
function snimy_baza_broker()
{
    global $db, $prs, $login, $pass, $count_page, $date_from, $date_to, $ch;
    global $kv_arr,$dom_arr,$bussines_arr,$ychastok_arr,$garazh_arr;
    $type_n = "snimy";
    $page = 1;
    $ch = curl_init();
    $ret = login($ch,"http://ua.baza-broker.com/login",'dp-avangard@rambler.ru','yaroslav');
    $ret = read($ch,"https://ua.baza-broker.com/index.php?do=baza&type=$type_n&page=$page&per_page=$count_page&dt=$date_from%2C$date_to");
    $html = new simple_html_dom();
    $html = str_get_html($ret);
    $col = $html->find('.objects_list_title_right > big',0)->plaintext;
    $pages = $col/$count_page;
    $pages = round($pages)+1;


    for ($i=1;$i<=$pages;$i++) {
        $ch = curl_init();
        $ret = login($ch, "http://ua.baza-broker.com/login", 'dp-avangard@rambler.ru', 'yaroslav');
        $ret = read($ch, "https://ua.baza-broker.com/index.php?do=baza&type=$type_n&page=$i&per_page=$count_page&dt=$date_from%2C$date_to");
        $html = new simple_html_dom();
        $html = str_get_html($ret);

        $tables = $html->find('div.offer_container');
        $rowData = array();
        $data_arr = array();
        $j = 0;

        foreach ($tables as $td) {


            $title = $td->find('div.objects_item_title', 0)->plaintext;

            $id = $td->find('div.objects_item_title', 0)->parent()->parent()->id;
            $id = explode("_", $id);
            $id = $id[1];
            $addr = $td->find('div.objects_item_addr');

            $city = "";
            $adraion = "";
            $mraion = "";
            $street = "";
            $house = "";
            $r = 0;

            $kichen = 0;
            $rooms = 0;
            $floor = 0;
            $floors = 0;
            $area = 0;
            $living_area = 0;
            $city = "---";
            $adraion = "---";
            $mraion = "---";
            $street = "---";
            $house = "---";

            $area_json = "$area / $living_area / $kichen";

            $price  = "цена договорная";

            $phone = "";

            $date_obj = $td->find('span.objects_item_dt_added', 1)->plaintext;

            $date = date("Y-m-d");
            $d = new DateTime($date);


            if (stripos($date_obj, 'сегодня') !== false) {

                $date_obj = str_replace("сегодня", $d->format("d-m-Y"), $date_obj);

            } elseif (stripos($date_obj, 'вчера') !== false) {
                $d->modify("-1 day");
                $date_obj = str_replace("вчера", $d->format("d-m-Y"), $date_obj);

            }

            $link = $td->find('a.objects_item_info_col_card_link', 0)->href;
            $link = "https://ua.baza-broker.com/" . $link;

            $text = $td->find('div.objects_item_info_col_comment_text', 0)->plaintext;
            $region = "Днепропетровская обл.";
            $city = 'Днепропетровск';
            $operation = 5;
            $photo_arr = "---";
            $estateType = "---";
            $marketSegment = 1;


            $flag = 0;

            $db_arr = array(
                'id' => strval($id) + 0,
                'frm' => 4,
                'region' => $region,
                'city' => str_replace(" ", "", $city),
                'adistrict' => $mraion,
                'district' => $adraion,
                'street' => $street,
                'rooms' => strval(str_replace("&nbsp;", "", $rooms)),
                'floor' => strval(str_replace("&nbsp;", "", $floor)),
                'max_floor' => strval(str_replace("&nbsp;", "", $floors)),
                'area' => strval(str_replace("&nbsp;", "", $area)),
                'area_json' => $area_json,
                'operation' => $operation,
                'marketSegment' => $marketSegment,
                'estateType' => $estateType,
                'adDate' => strtotime($date_obj),
                'phones' => $phone,
                'price' => strval(str_replace("&nbsp;", "", $price)),
                'text' => $text,
                'url' => $link,
                'images' => $photo_arr,
                'flags' => $flag,
            );
            $sql = "SELECT COUNT(*) as count from parsed WHERE id='$id' AND price='$price' AND phones = '$phone'";


            $sql_controll = $db->query($sql);


            //var_dump($db_arr);

            if ($sql_controll[0]['count'] != 0) {

                return Parser::Log('Dublicate info!');
            } else {


                $db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,floor,max_floor,area,area_json,operation,marketSegment,estateType,adDate,phones,price,text,url,images,flags)
                                  VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:floor,:max_floor,:area,:area_json,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:images,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                    $db_arr);

            }


        }


        // echo $html;
    }
}
///////////////////////////////////////////////////////////////////////////// Snimu end
///////////////////////////////////////////////////////////////////////////// kyply
function kypliy_baza_broker()
{
    global $db, $prs, $login, $pass, $count_page, $date_from, $date_to, $ch;
    global $kv_arr,$dom_arr,$bussines_arr,$ychastok_arr,$garazh_arr;
    $type_n = "kyply";
    $page = 1;
    $ch = curl_init();
    $ret = login($ch,"http://ua.baza-broker.com/login",'dp-avangard@rambler.ru','yaroslav');
    $ret = read($ch,"https://ua.baza-broker.com/index.php?do=baza&type=$type_n&page=$page&per_page=$count_page&dt=$date_from%2C$date_to");
    $html = new simple_html_dom();
    $html = str_get_html($ret);
    $col = $html->find('.objects_list_title_right > big',0)->plaintext;
    $pages = $col/$count_page;
    $pages = round($pages)+1;


    for ($i=1;$i<=$pages;$i++) {
        $ch = curl_init();
        $ret = login($ch, "http://ua.baza-broker.com/login", 'dp-avangard@rambler.ru', 'yaroslav');
        $ret = read($ch, "https://ua.baza-broker.com/index.php?do=baza&type=$type_n&page=$i&per_page=$count_page&dt=$date_from%2C$date_to");
        $html = new simple_html_dom();
        $html = str_get_html($ret);

        $tables = $html->find('div.offer_container');
        $rowData = array();
        $data_arr = array();
        $j = 0;

        foreach ($tables as $td) {


            $title = $td->find('div.objects_item_title', 0)->plaintext;

            $id = $td->find('div.objects_item_title', 0)->parent()->parent()->id;
            $id = explode("_", $id);
            $id = $id[1];
            $addr = $td->find('div.objects_item_addr');

            $city = "";
            $adraion = "";
            $mraion = "";
            $street = "";
            $house = "";
            $r = 0;

            $kichen = 0;
            $rooms = 0;
            $floor = 0;
            $floors = 0;
            $area = 0;
            $living_area = 0;
            $city = "---";
            $adraion = "---";
            $mraion = "---";
            $street = "---";
            $house = "---";

            $area_json = "$area / $living_area / $kichen";

            $price  = "цена договорная";

            $phone = "";

            $date_obj = $td->find('span.objects_item_dt_added', 1)->plaintext;

            $date = date("Y-m-d");
            $d = new DateTime($date);


            if (stripos($date_obj, 'сегодня') !== false) {

                $date_obj = str_replace("сегодня", $d->format("d-m-Y"), $date_obj);

            } elseif (stripos($date_obj, 'вчера') !== false) {
                $d->modify("-1 day");
                $date_obj = str_replace("вчера", $d->format("d-m-Y"), $date_obj);

            }

            $link = $td->find('a.objects_item_info_col_card_link', 0)->href;
            $link = "https://ua.baza-broker.com/" . $link;

            $text = $td->find('div.objects_item_info_col_comment_text', 0)->plaintext;
            $region = "Днепропетровская обл.";
            $city = 'Днепропетровск';
            $operation = 4;
            $photo_arr = "---";
            $estateType = "---";
            $marketSegment = 1;


            $flag = 0;

            $db_arr = array(
                'id' => strval($id) + 0,
                'frm' => 4,
                'region' => $region,
                'city' => str_replace(" ", "", $city),
                'adistrict' => $mraion,
                'district' => $adraion,
                'street' => $street,
                'rooms' => strval(str_replace("&nbsp;", "", $rooms)),
                'floor' => strval(str_replace("&nbsp;", "", $floor)),
                'max_floor' => strval(str_replace("&nbsp;", "", $floors)),
                'area' => strval(str_replace("&nbsp;", "", $area)),
                'area_json' => $area_json,
                'operation' => $operation,
                'marketSegment' => $marketSegment,
                'estateType' => $estateType,
                'adDate' => strtotime($date_obj),
                'phones' => $phone,
                'price' => strval(str_replace("&nbsp;", "", $price)),
                'text' => $text,
                'url' => $link,
                'images' => $photo_arr,
                'flags' => $flag,
            );
            $sql = "SELECT COUNT(*) as count from parsed WHERE id='$id' AND price='$price' AND phones = '$phone'";


            $sql_controll = $db->query($sql);


            //var_dump($db_arr); exit;

            if ($sql_controll[0]['count'] != 0) {

                return Parser::Log('Dublicate info!');
            } else {


                $db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,floor,max_floor,area,area_json,operation,marketSegment,estateType,adDate,phones,price,text,url,images,flags)
                                  VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:floor,:max_floor,:area,:area_json,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:images,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                    $db_arr);

            }


        }


        // echo $html;
    }
}
///////////////////////////////////////////////////////////////////////////// kyply end
function Old_baza_broker ()
{


    $headers = array(
        'Referer:http://baza-broker.com/',
        'X-Requested-With:XMLHttpRequest',
    );


//$data = array('login' => $login, 'password' => $pass);
    curl_setopt($ch, CURLOPT_URL, "http://baza-broker.com/login");
    curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_USERPWD, $login.':'.$pass);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'login=' . $login . '&password=' . $pass);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie1.txt");
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie1.txt");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36');
// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_REFERER, "https://ua.baza-broker.com/login?user=dp-avangard@rambler.ru&pass=yaroslav");
//curl_setopt($ch, CURLOPT_COOKIE, true);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, "http://baza-broker.com/index.php?do=baza&type=sale&page=&region=3&per_page=25&dt=2015-11-03%2C2015-11-06&sub_type=flat&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30");


    $content = curl_exec($ch);
    if (curl_error($ch)) {
        echo curl_error($ch);
    }
    curl_close($ch);
//echo $content;
    exit;


    $type = "sale";
    $page = 1;

    for ($i = 1; $i <= 1; $i++) {

        $headers = array(
            'Host:http://baza-broker.com',
            'X-Requested-With:XMLHttpRequest',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://baza-broker.com/index.php?do=baza&type=" . $type . "&page=" . $i . "&region=3&per_page=" . $count_page . "&dt=" . $date_from . "%2C" . $date_to . "&sub_type=flat&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30&view=table");
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // переходит по редиректам

        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie1.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie1.txt");

        $content = curl_exec($ch);

        curl_close($ch);


        $table_content = '/<table class="objects_items_list">(.*)<\/table>/usi';
        $announce_pattern = '/<tr id="offer_([0-9]*)" class="offer_container objects_item cleared">(.*)<td class="objects_item_info_col_9">(.*)<\/td>(.*)<\/tr>/Usi';
        //preg_match($table_content, $content, $match);
        preg_match_all($announce_pattern, $content, $match);


        $district_pattern = '/<div class="objects_item_addr">.*<a.*>(.*)<\/a>.*<\/div>/Usi';
        $type_pattern = '/<div class="objects_item_info_col_w">(.*)<\/div>/Usi';
        $comment_pattern = '/<div class="objects_item_info_col_comment_text no-truncate".*>(.*)<div>/Usi';
        $date_pattern = '/<span class="objects_item_dt_added">(.*)<\/span>/Usi';

        preg_match_all($comment_pattern, $content, $comment_match);
        preg_match_all('/<span class="objects_item_dt_added">(.*)<\/span>/Usi', $content, $date_match);


        if (!empty($match[1])) {
            foreach ($match[2] as $key => $val) {
                preg_match_all($district_pattern, $val, $district_match);
                preg_match_all($type_pattern, $val, $match2);
                if (strpos($match2[1][1], 'квартира')) {
                    $f_type = "Квартира";
                } else if (strpos($match2[1][1], 'дом')) {
                    $f_type = "Дом";
                } else {
                    $f_type = $val[1];
                }


                preg_match('/<tr.*>.*Общая:.*<td>([0-9]*)&nbsp;.*<\/tr>/Usi', $match2[1][4], $area_match);

                $rooms = $match2[1][2];
                preg_match('/<div class="objects_item_price">(.*)\$/Usi', $match2[1][3], $price_match);


                $floors = explode('/', $match2[1][6]);
                $floor = trim($floors[0]);
                $max_floor = trim($floors[1]);

                preg_match('/<strong>.*<a.*>(.*)<\/a>.*<\/strong>/Usi', $match2[1][8], $phone_match);


                $desc = trim($comment_match[1][$key]);
                $dt = trim($date_match[1][$key]);
                if ($dt == 'вчера') {
                    $date_cp = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
                } else if (strtotime($dt)) {
                    $date_cp = date('Y-m-d', strtotime($dt));
                } else {
                    $date_cp = date('Y-m-d H:i:s', time());
                }

                print_r($district_match[1]);
                $rooms = trim($rooms);
                $operat = 1;
                $city = trim($district_match[1][0]);
                $district = trim($district_match[1][1]);
                if (isset($district_match[1][2])) {
                    $street = trim($district_match[1][2]);
                } else {
                    $street = "---";
                }
                if (isset($area_match[1])) {
                    $area = trim($area_match[1]);
                } else {
                    $area = "0";
                }
                $estate_type = trim($f_type);
                $price = trim(str_replace(' ', '', $price_match[1]));
                $phones = array(substr($phone_match[1], 3));
                $area_json = $area . " / 0 / 0";
                $url = '';
                $adistrict = '---';
                $photos_json = '';

                $prs->sql_baza_broker_put_safe('', $date_cp, $price, $rooms, $operat, $city, $estate_type, $floor, $max_floor, $district, $street, $desc, $phones, 0, $area, $area_json, $url, $adistrict, $photos_json);
                //print_r($match2[1]);
                echo $district;

            }
        }
    }


    $type = "rent";
    $page = 1;

    for ($i = 1; $i <= 1; $i++) {

        $headers = array(
            'Host:http://baza-broker.com',
            'X-Requested-With:XMLHttpRequest',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://baza-broker.com/index.php?do=baza&type=" . $type . "&page=" . $i . "&region=3&per_page=" . $count_page . "&dt=" . $date_from . "%2C" . $date_to . "&sub_type=flat&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30&view=table");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie1.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie1.txt");

        $content = curl_exec($ch);
        curl_close($ch);


        $table_content = '/<table class="objects_items_list">(.*)<\/table>/usi';
        $announce_pattern = '/<tr id="offer_([0-9]*)" class="offer_container objects_item cleared">(.*)<td class="objects_item_info_col_9">.*<\/td>.*<\/tr>/Usi';
        //preg_match($table_content, $content, $match);
        preg_match_all($announce_pattern, $content, $match);

        //print_r($match[2][0]);

        $district_pattern = '/<div class="objects_item_addr">.*<a.*>(.*)<\/a>.*<\/div>/Usi';
        $type_pattern = '/<div class="objects_item_info_col_w">(.*)<\/div>/Usi';
        $comment_pattern = '/<div class="objects_item_info_col_comment_text no-truncate".*>(.*)<div>/Usi';
        $date_pattern = '/<span class="objects_item_dt_added">(.*)<\/span>/Usi';

        preg_match_all($comment_pattern, $content, $comment_match);
        preg_match_all('/<span class="objects_item_dt_added">(.*)<\/span>/Usi', $content, $date_match);


        if (!empty($match[1])) {
            foreach ($match[2] as $key => $val) {
                preg_match_all($district_pattern, $val, $district_match);
                preg_match_all($type_pattern, $val, $match2);
                if (strpos($match2[1][1], 'квартира')) {
                    $f_type = "Квартира";
                } else if (strpos($match2[1][1], 'дом')) {
                    $f_type = "Дом";
                } else {
                    $f_type = $val[1];
                }

                preg_match('/<tr.*>.*Общая:.*<td>([0-9]*)&nbsp;.*<\/tr>/Usi', $match2[1][4], $area_match);

                $rooms = $match2[1][2];
                preg_match('/<div class="objects_item_price">(.*)\грн/Usi', $match2[1][3], $price_match);


                $floors = explode('/', $match2[1][6]);
                $floor = trim($floors[0]);
                $max_floor = trim($floors[1]);

                preg_match('/<strong>.*<a.*>(.*)<\/a>.*<\/strong>/Usi', $match2[1][8], $phone_match);


                $desc = trim($comment_match[1][$key]);
                $dt = trim($date_match[1][$key]);
                if ($dt == 'вчера') {
                    $date_cp = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
                } else if (strtotime($dt)) {
                    $date_cp = date('Y-m-d', strtotime($dt));
                } else {
                    $date_cp = date('Y-m-d H:i:s', time());
                }

                print_r($district_match[1]);
                $rooms = trim($rooms);
                $operat = 2;
                if (strpos($match2[1][1], 'посуточно')) {
                    $operat = 3;
                }
                $city = trim($district_match[1][0]);
                $district = trim($district_match[1][1]);
                if (isset($district_match[1][2])) {
                    $street = trim($district_match[1][2]);
                } else {
                    $street = "---";
                }
                if (isset($area_match[1])) {
                    $area = trim($area_match[1]);
                } else {
                    $area = "0";
                }
                $estate_type = trim($f_type);
                $price = trim(str_replace(' ', '', $price_match[1]));
                $phones = array(substr($phone_match[1], 3));
                $area_json = $area . " / 0 / 0";
                $url = '';
                $adistrict = '---';
                $photos_json = '';
                echo $price;
                //echo
                $prs->sql_baza_broker_put_safe('', $date_cp, $price, $rooms, $operat, $city, $estate_type, $floor, $max_floor, $district, $street, $desc, $phones, 0, $area, $area_json, $url, $adistrict, $photos_json);
                //print_r($match2[1]);

                echo $district;
            }
        }
    }


    $type = "sale";
    $page = 1;

    for ($i = 1; $i <= 1; $i++) {

        $headers = array(
            'Host:http://baza-broker.com',
            'X-Requested-With:XMLHttpRequest',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://baza-broker.com/index.php?do=baza&type=" . $type . "&page=" . $i . "&region=3&per_page=" . $count_page . "&dt=" . $date_from . "%2C" . $date_to . "&sub_type=commercial&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30&view=table");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie1.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie1.txt");

        $content = curl_exec($ch);
        curl_close($ch);


        $table_content = '/<table class="objects_items_list">(.*)<\/table>/usi';
        $announce_pattern = '/<tr id="offer_([0-9]*)" class="offer_container objects_item cleared">(.*)<td class="objects_item_info_col_9">.*<\/td>.*<\/tr>/Usi';
        //preg_match($table_content, $content, $match);
        preg_match_all($announce_pattern, $content, $match);

        //print_r($match[2][0]);

        $district_pattern = '/<div class="objects_item_addr">.*<a.*>(.*)<\/a>.*<\/div>/Usi';
        $type_pattern = '/<div class="objects_item_info_col_w">(.*)<\/div>/Usi';
        $comment_pattern = '/<div class="objects_item_info_col_comment_text no-truncate".*>(.*)<div>/Usi';
        $date_pattern = '/<span class="objects_item_dt_added">(.*)<\/span>/Usi';

        preg_match_all($comment_pattern, $content, $comment_match);
        preg_match_all('/<span class="objects_item_dt_added">(.*)<\/span>/Usi', $content, $date_match);


        if (!empty($match[1])) {
            foreach ($match[2] as $key => $val) {
                preg_match_all($district_pattern, $val, $district_match);
                preg_match_all($type_pattern, $val, $match2);
                //preg_match('/<div class="objects_item_price">(.*)\$/Usi',$match2[1][3],$type_match);
                //print_r($match2[1][1]);exit;
                if (strpos($match2[1][1], 'квартира')) {
                    $f_type = "Квартира";
                } else if (strpos($match2[1][1], 'дом')) {
                    $f_type = "Дом";
                } else if (strpos($match2[1][1], 'гараж')) {
                    $f_type = "Гараж";
                } else {
                    $f_type = strip_tags($val[1]);
                }
                //print_r($f_type);exit;


                preg_match('/<tr.*>.*Общая:.*<td>([0-9]*)&nbsp;.*<\/tr>/Usi', $match2[1][4], $area_match);

                $rooms = $match2[1][2];
                preg_match('/<div class="objects_item_price">(.*)\$/Usi', $match2[1][3], $price_match);


                $floors = explode('/', $match2[1][6]);
                $floor = trim($floors[0]);
                $max_floor = trim($floors[1]);

                preg_match('/<strong>.*<a.*>(.*)<\/a>.*<\/strong>/Usi', $match2[1][8], $phone_match);


                $desc = trim($comment_match[1][$key]);
                $dt = trim($date_match[1][$key]);
                if ($dt == 'вчера') {
                    $date_cp = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
                } else if (strtotime($dt)) {
                    $date_cp = date('Y-m-d', strtotime($dt));
                } else {
                    $date_cp = date('Y-m-d H:i:s', time());
                }

                print_r($district_match[1]);
                $rooms = trim($rooms);
                $operat = 1;
                $city = trim($district_match[1][0]);
                $district = trim($district_match[1][1]);
                if (isset($district_match[1][2])) {
                    $street = trim($district_match[1][2]);
                } else {
                    $street = "---";
                }
                if (isset($area_match[1])) {
                    $area = trim($area_match[1]);
                } else {
                    $area = "0";
                }
                $estate_type = trim($f_type);
                $price = trim(str_replace(' ', '', $price_match[1]));
                $phones = array(substr($phone_match[1], 3));
                $area_json = $area . " / 0 / 0";
                $url = '';
                $adistrict = '---';
                $photos_json = '';
                echo $price;
                //echo
                $prs->sql_baza_broker_put_safe('', $date_cp, $price, $rooms, $operat, $city, $estate_type, $floor, $max_floor, $district, $street, $desc, $phones, 0, $area, $area_json, $url, $adistrict, $photos_json);
                //print_r($match2[1]);

                echo $district;
            }

        }
    }

    $type = "rent";
    $page = 1;

    for ($i = 1; $i <= 1; $i++) {

        $headers = array(
            'Host:http://baza-broker.com',
            'X-Requested-With:XMLHttpRequest',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://baza-broker.com/index.php?do=baza&type=" . $type . "&page=" . $i . "&region=3&per_page=" . $count_page . "&dt=" . $date_from . "%2C" . $date_to . "&sub_type=commercial&isset_photo=&price%5B%5D=&price%5B%5D=&currency=0&area%5B%5D=&area%5B%5D=&tarea%5B%5D=&tarea%5B%5D=&larea=&larea=&karea=&karea=&floor=0%3B30&floors=0%3B30&view=table");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie1.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie1.txt");

        $content = curl_exec($ch);
        curl_close($ch);


        $table_content = '/<table class="objects_items_list">(.*)<\/table>/usi';
        $announce_pattern = '/<tr id="offer_([0-9]*)" class="offer_container objects_item cleared">(.*)<td class="objects_item_info_col_9">.*<\/td>.*<\/tr>/Usi';
        //preg_match($table_content, $content, $match);
        preg_match_all($announce_pattern, $content, $match);

        //print_r($match[2][0]);

        $district_pattern = '/<div class="objects_item_addr">.*<a.*>(.*)<\/a>.*<\/div>/Usi';
        $type_pattern = '/<div class="objects_item_info_col_w">(.*)<\/div>/Usi';
        $comment_pattern = '/<div class="objects_item_info_col_comment_text no-truncate".*>(.*)<div>/Usi';
        $date_pattern = '/<span class="objects_item_dt_added">(.*)<\/span>/Usi';

        preg_match_all($comment_pattern, $content, $comment_match);
        preg_match_all('/<span class="objects_item_dt_added">(.*)<\/span>/Usi', $content, $date_match);


        if (!empty($match[1])) {
            foreach ($match[2] as $key => $val) {
                preg_match_all($district_pattern, $val, $district_match);
                preg_match_all($type_pattern, $val, $match2);
                //preg_match('/<div class="objects_item_price">(.*)\$/Usi',$match2[1][3],$type_match);
                //print_r($match2[1][1]);exit;
                if (strpos($match2[1][1], 'квартира')) {
                    $f_type = "Квартира";
                } else if (strpos($match2[1][1], 'дом')) {
                    $f_type = "Дом";
                } else if (strpos($match2[1][1], 'гараж')) {
                    $f_type = "Гараж";
                } else {
                    $f_type = strip_tags($val[1]);
                }
                //print_r($f_type);exit;


                preg_match('/<tr.*>.*Общая:.*<td>([0-9]*)&nbsp;.*<\/tr>/Usi', $match2[1][4], $area_match);

                $rooms = $match2[1][2];
                preg_match('/<div class="objects_item_price">(.*)\грн/Usi', $match2[1][3], $price_match);


                $floors = explode('/', $match2[1][6]);
                $floor = trim($floors[0]);
                $max_floor = trim($floors[1]);

                preg_match('/<strong>.*<a.*>(.*)<\/a>.*<\/strong>/Usi', $match2[1][8], $phone_match);


                $desc = trim($comment_match[1][$key]);
                $dt = trim($date_match[1][$key]);
                if ($dt == 'вчера') {
                    $date_cp = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
                } else if (strtotime($dt)) {
                    $date_cp = date('Y-m-d', strtotime($dt));
                } else {
                    $date_cp = date('Y-m-d H:i:s', time());
                }

                print_r($district_match[1]);
                $rooms = trim($rooms);
                $operat = 2;
                $city = trim($district_match[1][0]);
                $district = trim($district_match[1][1]);
                if (isset($district_match[1][2])) {
                    $street = trim($district_match[1][2]);
                } else {
                    $street = "---";
                }
                if (isset($area_match[1])) {
                    $area = trim($area_match[1]);
                } else {
                    $area = "0";
                }
                $estate_type = trim($f_type);
                $price = trim(str_replace(' ', '', $price_match[1]));
                $phones = array(substr($phone_match[1], 3));
                $area_json = $area . " / 0 / 0";
                $url = '';
                $adistrict = '---';
                $photos_json = '';
                echo $price;
                //echo
                $prs->sql_baza_broker_put_safe('', $date_cp, $price, $rooms, $operat, $city, $estate_type, $floor, $max_floor, $district, $street, $desc, $phones, 0, $area, $area_json, $url, $adistrict, $photos_json);
                //print_r($match2[1]);

                echo $district;
            }

        }
    }


//print_r($content);

//print_r($table_content);

//echo "3";

}
?>