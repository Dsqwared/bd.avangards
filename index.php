<?php

include_once "checklogin.php";
//include_once "district.php";
include_once "parser/districts.php";
include_once "parser/estateType.php";
require_once ('parser/modules/PHPExcel.php');
require_once ('parser/modules/PHPExcel/Writer/Excel5.php');

$time1 = date("Y-m-d H:i:s");
print_r("0 - $time1");
$time1 = date("Y-m-d H:i:s");
$cc = checklogin();


if (!$cc) {
    header("Location: http://www.avangards.com.ua/my-profile");
    exit();
}
if (isset($_GET['pass'])) {

    //header("Location: http://bd.avangards.com.ua/index.php");
    //exit();
}

function typeop($id)
{
    switch ($id) {
        case 1:
            return 'Продам';
        case 2:
            return 'Сдам';
        case 3:
            return 'Сдам посуточно';
        case 4:
            return 'Куплю';
        case 5:
            return 'Сниму';
        default:
            return '---';
    }
}

function formExcel($data)
{
    $xls = new PHPExcel();
    // Устанавливаем индекс активного листа
    $xls->setActiveSheetIndex(0);
    $sheet = $xls->getActiveSheet();
    // Подписываем лист
    $sheet->setTitle('Экспорт объектов');
    $xls->getDefaultStyle()->getFont()->setName('Arial');
    $xls->getDefaultStyle()->getFont()->setSize(8);
    // Вставляем текст в ячейку A1
    //$sheet->setCellValue("A1", 'Таблица объектов. Агенство недвижимости "Авангард"');
    // Объединяем ячейки
//$sheet->mergeCells('A1:H1');

    //$sheet->setCellValue("A1", 'ID');
    //$sheet->setCellValue("A1", 'Админрайон');
    $sheet->setCellValue("A1", 'Микройрайон');
    $sheet->setCellValue("B1", 'Улица');
    $sheet->setCellValue("C1", 'Тип');
    $sheet->setCellValue("D1", 'Комнат');
    $sheet->setCellValue("E1", 'Этаж');
    $sheet->setCellValue("F1", 'Этажность');
    $sheet->setCellValue("G1", 'Площадь');


    if ($data[0]['operation'] == 1 || $data[0]['operation'] == 4) {
        $price = 'Цена, $';
    } else {
        $price = 'Цена, грн';
    }

    $sheet->setCellValue("H1", $price);
    $sheet->setCellValue("I1", 'Телефон');
    $sheet->setCellValue("J1", 'Дата');
    $sheet->setCellValue("K1", 'Метка');
    //$sheet->setCellValue("N1", 'Ссылка');
    $sheet->setCellValue("L1", 'Описание');
    $index = 2;
    $alp_in = 0;
    $m = 1;
    $border_style1 = array('borders' => array('right' => array('style' =>
        PHPExcel_Style_Border::BORDER_THICK, 'color' => array('argb' => '0xeceff2'))));

    $border_style = array('borders' => array('right' => array('style' =>
        PHPExcel_Style_Border::BORDER_THICK, 'color' => array('argb' => '0xfafafa'))));

    //$alphabet_en = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    foreach ($data as $offer) {
//$xls->getActiveSheet()->getRowDimension($index)->setRowHeight(30);
        $xls->getActiveSheet()->getRowDimension($index)->setRowHeight(-1);

        $xls->getActiveSheet()->getStyle('A' . $index . ':K' . $index)
            ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $xls->getActiveSheet()->getStyle('L' . $index)
            ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $xls->getActiveSheet()->getStyle('D' . $index . ':K' . $index)
            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $xls->getActiveSheet()->getStyle('A' . $index . ':C' . $index)
            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $xls->getActiveSheet()->getStyle('L' . $index)
            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        if ($m === 1) {
            $xls->getActiveSheet()->getStyle('A' . $index . ':L' . $index)->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setARGB('0xfafafa');
            $m = 0;
            $xls->getActiveSheet()->getStyle('A' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('B' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('C' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('D' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('E' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('F' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('G' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('H' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('I' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('J' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('K' . $index)->applyFromArray($border_style1);
            $xls->getActiveSheet()->getStyle('L' . $index)->applyFromArray($border_style1);
            //$xls->getActiveSheet()->getStyle('M'.$index)->applyFromArray($border_style1);
            //$xls->getActiveSheet()->getStyle('N'.$index)->applyFromArray($border_style1);
            //$xls->getActiveSheet()->getStyle('O'.$index)->applyFromArray($border_style1);
        } else {
            $xls->getActiveSheet()->getStyle('A' . $index . ':L' . $index)->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setARGB('0xeceff2');
            $m = 1;
            $xls->getActiveSheet()->getStyle('A' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('B' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('C' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('D' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('E' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('F' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('G' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('H' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('I' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('J' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('K' . $index)->applyFromArray($border_style);
            $xls->getActiveSheet()->getStyle('L' . $index)->applyFromArray($border_style);
            //$xls->getActiveSheet()->getStyle('M'.$index)->applyFromArray($border_style);
            //$xls->getActiveSheet()->getStyle('N'.$index)->applyFromArray($border_style);
            //$xls->getActiveSheet()->getStyle('O'.$index)->applyFromArray($border_style);
        }

        //$sheet->setCellValue("A".$index, $offer['id_mn']);
        //$sheet->setCellValue("A".$index, $offer['adistrict']);
        $sheet->setCellValue("A" . $index, $offer['district']);
        $sheet->setCellValue("B" . $index, $offer['street']);
        $sheet->setCellValue("C" . $index, $offer['estateType']);
        $sheet->setCellValue("D" . $index, $offer['rooms']);
        $sheet->setCellValue("E" . $index, $offer['floor']);
        $sheet->setCellValue("F" . $index, $offer['max_floor']);
        $sheet->setCellValue("G" . $index, $offer['area_json']);
        $sheet->setCellValue("H" . $index, $offer['price']);
        $sheet->setCellValue("I" . $index, $offer['phones']);
        $sheet->setCellValue("J" . $index, date("d/m/Y H:i:s", $offer['adDate']));
        $sheet->setCellValue("K" . $index, getFlag($offer['flags']));
        //$sheet->setCellValue("M".$index, $offer['url']);
        $sheet->setCellValue("L" . $index, $offer['text']);


        $index++;
    }
// Выравнивание текста
//$sheet->getStyle('A1')->getAlignment()->setHorizontal(
    //  PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $xls->getActiveSheet()->getStyle('A1:L1')->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('0x56636b');

    $xls->getActiveSheet()->getStyle('A1:L1')
        ->getFont()->getColor()->setARGB('ffffffff');

    //$xls->getActiveSheet()->getColumnDimension('A')->setWidth(8);
    //$xls->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $xls->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $xls->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $xls->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $xls->getActiveSheet()->getColumnDimension('D')->setWidth(6);
    $xls->getActiveSheet()->getColumnDimension('E')->setWidth(6);
    $xls->getActiveSheet()->getColumnDimension('F')->setWidth(6);
    $xls->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $xls->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $xls->getActiveSheet()->getColumnDimension('I')->setWidth(12);
    $xls->getActiveSheet()->getColumnDimension('J')->setWidth(12);
    $xls->getActiveSheet()->getColumnDimension('K')->setWidth(20);
//	$xls->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    $xls->getActiveSheet()->getColumnDimension('L')->setWidth(30);

    $xls->getActiveSheet()->getRowDimension('1')->setRowHeight(25);

    //$xls->getActiveSheet()->getStyle('A2:O'.$index)
    //->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

    $xls->getActiveSheet()->getStyle('L1:L' . $xls->getActiveSheet()->getHighestRow())
        ->getAlignment()->setWrapText(true);

    $xls->getActiveSheet()->getStyle('I1:I' . $xls->getActiveSheet()->getHighestRow())
        ->getAlignment()->setWrapText(true);

    $xls->getActiveSheet()->getStyle('J1:J' . $xls->getActiveSheet()->getHighestRow())
        ->getAlignment()->setWrapText(true);

    $xls->getActiveSheet()->setAutoFilter(
        $xls->getActiveSheet()
            ->calculateWorksheetDimension()
    );

    // Выводим HTTP-заголовки
    header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=data.xls");

// Выводим содержимое файла
    $objWriter = new PHPExcel_Writer_Excel5($xls);
    $objWriter->save('php://output');
}

function getFlag($flag)
{
    switch ($flag) {
        case '0':
            $resp = "Собственник";
            break;
        case '1':
            $resp = "Перезвонить";
            break;
        case '2':
            $resp = "Актуально";
            break;
        case '3':
            $resp = "Не актуально";
            break;
        case '4':
            $resp = "Риэлтор";
            break;
        case '5':
            $resp = "Моя метка";
            break;
        case '6':
            $resp = "Моя метка";
            break;
        case '7':
            $resp = "Вероятно риэлтор";
            break;
        case '8':
            $resp = "Эксклюзив риэлтора";
            break;
    }

    return $resp;
}

function export($list)
{

    $fp = fopen('export.csv', 'w');
    fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
    foreach ($list as $fields) {
        fputcsv($fp, array_values($fields));
    }
    fclose($fp);
    // system("ssconvert export.csv export.xls");

    header("Location: export.csv");
    exit();
}

require_once("Db.class.php");
$db = new Db();
$db1 = new Db();

$op = 1;
$sql = "";
$sql1 = "SELECT * FROM `parsed` main LEFT JOIN (SELECT id_mn as idmn FROM `comments`) comments1 ON main.id_mn=comments1.idmn
 LEFT JOIN (SELECT post_id, user_id FROM `watched_row`) wr ON main.id_mn=wr.post_id 
 WHERE operation=$op ";
$sql2 = "SELECT COUNT(*) as entries FROM parsed WHERE operation=$op ";

$page = 0;
if (isset($_GET['page']) && isset($_GET['pager_active']) && $_GET['pager_active'] == 'true') $page = $_GET['page'];
$page_c = $page;
$page *= 50;
$tp = $page + 50;

//$today = strtotime(date('Y-m-d'));
$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

if (isset($_GET['op'])) $op = $_GET['op'];
$db->bind("op", $op);
$db1->bind("op", $op);

if (!isset($_GET['uid']) || empty($_GET['uid'])) :

    if (isset($_GET['fl_mic']) && !empty($_GET['fl_mic'])) {
        //$db->bind("fl_mic","%".$_GET['fl_mic']."%");
        //$sql.=' AND district LIKE :fl_mic';
        $arr_fl_mic = $_GET['fl_mic'];
        $arr_fl_mic1 = $arr_fl_mic;
        if (count($arr_fl_mic) >= 1) {
            $fl_mic_ = array_shift($arr_fl_mic);
            //$db->bind("frm",$frm_);
            $sql .= " AND (district LIKE '%" . addslashes($fl_mic_) . "%'";
            if (count($arr_fl_mic) >= 1) {
                foreach ($arr_fl_mic as $fl_mic_) {
                    //$db->bind("frm",$frm_);
                    $sql .= " OR district LIKE '%" . addslashes($fl_mic_) . "%'";
                }
            }
            $sql .= ")";
        }
    }

    if (isset($_GET['fl_street']) && $_GET['fl_street'] != '') {
        $db->bind("fl_street", "%" . $_GET['fl_street'] . "%");
        $db1->bind("fl_street", "%" . $_GET['fl_street'] . "%");
        $sql .= ' AND street LIKE :fl_street';
    }

    $_GET['fl_city'] = (isset($_GET['fl_city']) && !empty($_GET['fl_city'])) ? $_GET['fl_city'] : 'Днепропетровск';
    // $db->bind("fl_city",$_GET['fl_city']);
    // $db1->bind("fl_city",$_GET['fl_city']);
    if ($_GET['fl_city'] == "Днепропетровск") {
        $sql .= ' AND city = "Днепропетровск"';
    } else if ($_GET['fl_city'] == "Пригород") {
        $sql .= ' AND city != "Днепропетровск"';
    }


    /*if (isset($_GET['fl_type']) && $_GET['fl_type']!='') {
    //$db->bind("fl_type", );
    $t = addslashes($_GET['fl_type']);
    $sql.=' AND estateType = $t';
   }*/
//print_r($_GET['fl_type']);exit;
    if (isset($_GET['fl_type']) && !empty($_GET['fl_type'])) {

        $arr_fl_type = $_GET['fl_type'];
        $arr_fl_type1 = $arr_fl_type;
        if (count($arr_fl_type) >= 1) {
            $fl_type_ = array_shift($arr_fl_type);
            //$db->bind("frm",$frm_);
            $sql .= " AND (estateType='$fl_type_'";
            if (count($arr_fl_type) >= 1) {
                foreach ($arr_fl_type as $fl_type_) {
                    //$db->bind("frm",$frm_);
                    $sql .= " OR estateType='$fl_type_'";
                }
            }
            $sql .= ")";
        }
    }

    /*if (isset($_GET['fl_rooms']) && $_GET['fl_rooms']!='') {
     $db->bind("fl_rooms",$_GET['fl_rooms']);
     $sql.=' AND rooms=:fl_rooms';
    }*/


    if (isset($_GET['area_from']) && !empty($_GET['area_from'])) {
        //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
        $sql .= ' AND area >= ' . $_GET['area_from'];
    }

    if (isset($_GET['area_to']) && !empty($_GET['area_to'])) {
        //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
        $sql .= ' AND area <= ' . $_GET['area_to'];
    }

    if (isset($_GET['fl_floor_from']) && !empty($_GET['fl_floor_from'])) {
        //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
        $sql .= ' AND floor >= ' . $_GET['fl_floor_from'];
    }

    if (isset($_GET['fl_floor_to']) && !empty($_GET['fl_floor_to'])) {
        //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
        $sql .= ' AND floor <= ' . $_GET['fl_floor_to'];
    }

    if (isset($_GET['fl_floor_all_from']) && !empty($_GET['fl_floor_all_from'])) {
        //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
        $sql .= ' AND max_floor >= ' . $_GET['fl_floor_all_from'];
    }

    if (isset($_GET['fl_floor_all_to']) && !empty($_GET['fl_floor_all_to'])) {
        //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
        $sql .= ' AND max_floor <= ' . $_GET['fl_floor_all_to'];
    }

    if (isset($_GET['fl_price_from']) && !empty($_GET['fl_price_from'])) {
        //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
        $sql .= ' AND price >= ' . $_GET['fl_price_from'];
    }

    if (isset($_GET['fl_price_to']) && !empty($_GET['fl_price_to'])) {
        //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
        $sql .= ' AND price <= ' . $_GET['fl_price_to'];
    }

    if (isset($_GET['fl_rooms']) && !empty($_GET['fl_rooms'])) {
        $arr_fl_rooms = $_GET['fl_rooms'];
        if (count($arr_fl_rooms) >= 1) {
            $arr_fl_rooms_ = array_shift($arr_fl_rooms);
            //$db->bind("frm",$frm_);
            if ($arr_fl_rooms_ != 6) {
                $sql .= " AND (rooms='$arr_fl_rooms_'";
            } else {
                $sql .= " AND (rooms >= '$arr_fl_rooms_'";
            }
            if (count($arr_fl_rooms) >= 1) {
                foreach ($arr_fl_rooms as $fl_rooms_) {
                    //$db->bind("frm",$frm_);
                    if ($fl_rooms_ != 6) {
                        $sql .= " OR rooms='$fl_rooms_'";
                    } else {
                        $sql .= " OR rooms >= '$fl_rooms_'";
                    }
                }
            }
            $sql .= ")";
        }
    }


    if (isset($_GET['fl_phone']) && $_GET['fl_phone'] != '') {
        $db->bind("fl_phone", "%" . $_GET['fl_phone'] . "%");
        $db1->bind("fl_phone", "%" . $_GET['fl_phone'] . "%");
        $sql .= ' AND phones LIKE :fl_phone';
    }

    if (isset($_GET['fl_date_from']) && $_GET['fl_date_from'] != '') {
        $db->bind("fl_date_from", strtotime($_GET['fl_date_from']));
        $db1->bind("fl_date_from", strtotime($_GET['fl_date_from']));
        $sql .= ' AND adDate>=:fl_date_from';
    }

    if (isset($_GET['fl_date_to']) && $_GET['fl_date_to'] != '') {
        $db->bind("fl_date_to", strtotime($_GET['fl_date_to']));
        $db1->bind("fl_date_to", strtotime($_GET['fl_date_to']));
        $sql .= ' AND adDate<=:fl_date_to';
    }

    if (isset($_GET['fl_date_range']) && !empty($_GET['fl_date_range'])) {
        $arr_fl_dates = $_GET['fl_date_range'];
        if (count($arr_fl_dates) >= 1) {
            $arr_fl_dates_ = array_shift($arr_fl_dates);
            $tmp_parse_time = date_parse($arr_fl_dates_);
            $tmp_time_next = mktime(0, 0, 0, $tmp_parse_time['month'] + 1, $tmp_parse_time['day'], $tmp_parse_time['year']);
            $tmp_time_prev = mktime(0, 0, 0, $tmp_parse_time['month'], $tmp_parse_time['day'], $tmp_parse_time['year']);
            //$db->bind("frm",$frm_);
            $sql .= " AND ((adDate<'$tmp_time_next' AND adDate > '$tmp_time_prev')";
            if (count($arr_fl_dates) >= 1) {
                foreach ($arr_fl_dates as $fl_dates_) {
                    $tmp_parse_time = date_parse($fl_dates_);
                    $tmp_time_next = mktime(0, 0, 0, $tmp_parse_time['month'] + 1, $tmp_parse_time['day'], $tmp_parse_time['year']);
                    $tmp_time_prev = mktime(0, 0, 0, $tmp_parse_time['month'], $tmp_parse_time['day'], $tmp_parse_time['year']);
                    $sql .= " OR (adDate<'$tmp_time_next' AND adDate > '$tmp_time_prev')";
                }
            }
            $sql .= ")";
        }
    }


    if (isset($_GET['fl_date_r']) && $_GET['fl_date_r'] != '') {
        switch ($_GET['fl_date_r']) {
            case 'fl_date_today':
                $db->bind("fl_date_from", $today);
                $db1->bind("fl_date_from", $today);
                $sql .= ' AND adDate>=:fl_date_from';
                break;
            case 'fl_date_yesterday':
                $fl_date_yesterday = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
                $db->bind("fl_date_from", $fl_date_yesterday);
                $db1->bind("fl_date_from", $fl_date_yesterday);
                $db->bind("fl_date_to", $today);
                $db1->bind("fl_date_to", $today);
                $sql .= ' AND adDate<=:fl_date_to AND adDate>=:fl_date_from';
                break;
            case 'fl_date_week':
                $fl_date_week = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
                $db->bind("fl_date_from", $fl_date_week);
                $db1->bind("fl_date_from", $fl_date_week);
                $sql .= ' AND adDate>=:fl_date_from';
                break;
            case 'fl_date_month':
                $fl_date_month = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
                $db->bind("fl_date_from", $fl_date_month);
                $db1->bind("fl_date_from", $fl_date_month);
                $sql .= ' AND adDate>=:fl_date_from';
                break;
            case 'fl_date_kvartal':
                $fl_date_kvartal = mktime(0, 0, 0, date("m") - 3, date("d"), date("Y"));
                $db->bind("fl_date_from", $fl_date_kvartal);
                $db1->bind("fl_date_from", $fl_date_kvartal);
                $sql .= ' AND adDate>=:fl_date_from';
                break;
            case 'fl_date_halfyear':
                $fl_date_halfyear = mktime(0, 0, 0, date("m") - 6, date("d"), date("Y"));
                $db->bind("fl_date_from", $fl_date_halfyear);
                $db1->bind("fl_date_from", $fl_date_halfyear);
                $sql .= ' AND adDate>=:fl_date_from';
                break;
            case 'fl_date_year':
                $fl_date_year = mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1);
                $db->bind("fl_date_from", $fl_date_year);
                $db1->bind("fl_date_from", $fl_date_year);
                $sql .= ' AND adDate>=:fl_date_from';
                break;
        }
    }

    if (isset($_GET['fl_metka']) && !empty($_GET['fl_metka'])) {
        $arr_fl_metka = $_GET['fl_metka'];
        if (count($arr_fl_metka) >= 1) {
            $frm1_ = array_shift($arr_fl_metka);
            //$db->bind("frm",$frm_);
            $sql .= " AND (flags='$frm1_'";
            if (count($arr_fl_metka) >= 1) {
                foreach ($arr_fl_metka as $frm1_) {
                    //$db->bind("frm",$frm_);
                    $sql .= " OR flags='$frm1_'";
                }
            }
            $sql .= ")";
        }
    }

    if (isset($_GET['frm']) && !empty($_GET['frm'])) {
        $arr_frm = $_GET['frm'];
        if (count($arr_frm) >= 1) {
            $frm_ = array_shift($arr_frm);
            //$db->bind("frm",$frm_);
            $sql .= " AND (frm='$frm_'";
            if (count($arr_frm) >= 1) {
                foreach ($arr_frm as $frm_) {
                    //$db->bind("frm",$frm_);
                    $sql .= " OR frm='$frm_'";
                }
            }
            $sql .= ")";
        }
    }
    //$db1 = $db;
    $time1 = date("Y-m-d H:i:s");
    print_r("<br>$sql1 . $sql .  'ORDER BY adDate DESC LIMIT $page,50'");
    exit();
    $offers = $db->query($sql1 . $sql . " ORDER BY adDate DESC LIMIT $page,50");

    $entries = $db1->query($sql2 . $sql);
//print_r($pages_pag);
else:

    $uid = $_GET['uid'];
    $sql .= "AND `id_mn` LIKE '$uid%'";
    $offers = $db->query($sql1 . $sql . " ORDER BY adDate DESC LIMIT $page,50");

    $entries = $db1->query($sql2 . $sql);
endif;

$stats = $db->query('SELECT * FROM stats WHERE id=1');
$user_id = str_replace("user", "", $_COOKIE['bd_usr']);
$filters = $db->query('SELECT * FROM `saved_filters` where user_id ='.$user_id);

$pages_pag = round($entries[0]['entries'] / 50);

$ajax = false;

if (isset($_GET['ajax'])) $ajax = true;

if (isset($_GET['export'])) export($offers);

if (isset($_GET['exportExcel'])) {
    formExcel($offers);
    exit;
}

// $admin_place=$db->query("SELECT district FROM parsed GROUP BY district");
// var_dump($admin_place);
if (!$ajax) {
    ?>
    <!DOCTYPE HTML>
    <html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>База данных</title>
        <!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">-->
        <!-- Bootstrap -->
        <!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">-->
        <!-- Bootstrap -->
        <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link rel="stylesheet" href="css/chosen.min.css">
        <link rel="stylesheet" href="css/custom.css">
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>-->
        <script src="js/jquery-1.11.3.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>
        <!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
        <script src="js/i18datepicker.js"></script>
        <script src="js/chosen.jquery.min.js"></script>

        <link rel="stylesheet" href="fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen"/>
        <script type="text/javascript" src="fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
         <style type="text/css">
             .row-fiter-left{
                 display: inline-block;
                 float: left;
             }
             .row-fiter-left select{
                 width: 157px;
             }


             .bg-base1{
                 display: inline-block;
                 float: left!important;
             }
             .bg-base2{
                 float: right!important;
             }
             .row-fiter-right{
                 height: 28px;
                 width: 51%;
                 float: left;
                 display: block;
             }
         </style>
    </head>
    <body>

    <div class="container">
    <div class="row-centered ">
        <div class="col-centered" style="margin-bottom: 5px;">

            <!--<img src="http://avangards.com.ua/sites/all/themes/avangards03a/images/object2007797612.png">-->
            <div style="float:left;padding-top: 12px;margin-right: 12px;">
                <a href="http://www.avangards.com.ua/my-profile"><img src="images/logo.jpg" height="40px"></a>
            </div>
            <div style="float:left">
                <h3>САМАЯ БОЛЬШАЯ БАЗА ОБЪЕКТОВ НЕДВИЖИМОСТИ В ДНЕПРОПЕТРОВСКЕ</h3>
            </div>
        </div>
    </div>
    <div class="row">

    <div style="padding-top:5px;">

        <ul class="nav nav-pills pull-left">
            <li <?php if ($op == 1) echo 'class="active"' ?>><a href="?op=1">Продам</a></li>
            <li <?php if ($op == 2) echo 'class="active"' ?>><a href="?op=2">Сдам</a></li>
            <li <?php if ($op == 3) echo 'class="active"' ?>><a href="?op=3">Сдам посуточно</a></li>
            <li <?php if ($op == 4) echo 'class="active"' ?>><a href="?op=4">Куплю</a></li>
            <li <?php if ($op == 5) echo 'class="active"' ?>><a href="?op=5">Сниму</a></li>
        </ul>
        <!--<div class="pull-right">
                <input class="form-control" type="submit" onClick="export_exel()" value="Експорт CSV" style="margin-top:3px">
            </div>-->
        <div class="pull-right float">
            <input class="form-control" type="submit" onClick="exporttoexcel()" value="Экспорт XLS"
                   style="margin-top:3px">
        </div>
        <div class="pull-right float">
            <input class="form-control" type="button" onClick="showStats()" value="Статистика" style="margin-top:3px">
        </div>
        <div class="pull-right float">
            <a href="https://docs.google.com/spreadsheets/d/11Fgbn0wFQluQup5LUUvq098NBCczkClYJFAgZw4wZdI/edit#gid=241804067"
               class="form-control" style="margin-top:3px" target="_blank">Аналитика цен</a>
        </div>
        <!--<div class="pull-right float">
            <a href="https://drive.google.com/folderview?id=0B9UtDVocU5ENZGJMVmVsdGlON1U&usp=sharing"
               class="form-control" style="margin-top:3px" target="_blank">Материалы</a>
        </div> -->
       <!-- <div class="pull-right float">
            <a href="https://docs.google.com/spreadsheets/d/17-_Pgd05K-9m--ZiBdVsLQpiVsR1OMNRgw54M75lsNg/edit#gid=293202143"
               class="form-control" style="margin-top:3px" target="_blank">Клиентская База</a>
        </div> -->
        <!--<div class="pull-right float">
            <a href="#" class="form-control" id="add_g_entry" style="margin-top:3px">+</a>
        </div>-->
    </div>
    <div class="clearfix bg-info"></div><br>


    <div class="form-div" style="overflow:hidden;width:100%;height: auto;">
        <form method="get" id="frm_filter" class="form-inline">
            <div class="container">
                <div class="row">
                <div class="row-centered col-md-6 col-lg-6 col-xs-12">
                    <div class="col-centered" style="margin-top:0px;">
                        <input class="btn btn-default" type="submit" value="Фильтровать">
                        <input class="btn btn-primary reset-filter" type="button" value="Обнулить фильтр">
                        <select class="btn btn-default" style="width: 100px; height: 37px;" id="select_filter">
                            <option class="fls" data-id="0" value="?" <?php if (!isset($_GET['filter_id'])) {
                                echo 'selected="selected"'; } ?>>Фильтр</option>
                            <?php
                            foreach ($filters as $filter_) {
                                ?>
                                <?php if (isset($_GET['filter_id']) && $_GET['filter_id'] == $filter_['id']) { ?>
                                    <option class="fls" value="<?= $filter_['query'] ?>" data-id="<?= $filter_['id'] ?>"
                                            selected="selected"><?= $filter_['name'] ?></option>
                                    <?php
                                } else { ?>
                                    <option class="fls" value="<?= $filter_['query'] ?>"
                                            data-id="<?= $filter_['id'] ?>"><?= $filter_['name'] ?></option>
                                <?php }
                            }
                            ?>

                        </select>
                        <?php if (!isset($_GET['filter_id']) || $_GET['filter_id'] == 0) { ?>
                            <input class="btn btn-primary save-filter" type="button" disabled="disabled"
                                   value="Сохранить фильтр">
                        <?php } else { ?>
                            <input class="btn btn-primary remove-filter" id="r_filter" type="button"
                                   data-id="<?= $_GET['filter_id'] ?>" value="Удалить фильтр">
                        <?php } ?>
                        <input class="btn" id="name_filter" type="text" placeholder="Название фильтра"
                               style="display:none">
                        <input class="btn btn-primary" id="save_filter" type="button" value="Сохранить фильтр"
                               style="display:none">
                    </div>
                </div>
                <legend class="col-md-6 col-lg-6 col-xs-12" style="height:28px">
                    <div class="pull-right bg-warning bg-base1" style="font-size:12px;padding: 0 6px;">
                        <!--<label class="l-radio">Города для поиска</label>-->
                        <input type="radio" name="fl_city"
                               value="Днепропетровск" <?php if ((isset($_GET['fl_city']) && $_GET['fl_city'] == 'Днепропетровск') || !isset($_GET['fl_city'])) {
                            echo 'checked="checked"';
                        } ?> class="frm_ch1"/> <span class="l-radio">Днепропетровск</span>
                        <input type="radio" name="fl_city"
                               value="Пригород" <?php if ((isset($_GET['fl_city']) && $_GET['fl_city'] == 'Пригород')) {
                            echo 'checked="checked"';
                        } ?> class="frm_ch1"/> <span class="l-radio">Пригород</span>
                        <!--
								<input type="radio" name="fl_city" value="Днепродзержинск" <?php if ((isset($_GET['fl_city']) && $_GET['fl_city'] == 'Днепродзержинск')) {
                            echo 'checked="checked"';
                        } ?> class="frm_ch1"  /> <span class="l-radio">Днепродзержинск</span>
								<input type="radio" name="fl_city" value="Новомосковск" <?php if ((isset($_GET['fl_city']) && $_GET['fl_city'] == 'Новомосковск')) {
                            echo 'checked="checked"';
                        } ?> class="frm_ch1"  /> <span class="l-radio">Новомосковск</span>-->
                    </div>
                    <div class="pull-right bg-warning bg-base2" style="font-size:12px;margin-left: 20px;padding: 0 6px;">
                        <!-- <label class="l-radio">База данных</label> -->
                        <input type="checkbox" name="frm[0]"
                               value="1" <?php if ((isset($_GET['frm'][0]) && $_GET['frm'][0] == 1) || !isset($_GET['frm'])) {
                            echo 'checked="checked"';
                        } ?> class="frm_ch"/> <span class="l-radio">Квадрат</span>

                        <input type="checkbox" name="frm[1]"
                               value="2" <?php if ((isset($_GET['frm'][1]) && $_GET['frm'][1] == 2) || !isset($_GET['frm'])) {
                            echo 'checked="checked"';
                        } ?> class="frm_ch"/> <span class="l-radio">Ray-2</span>

                        <input type="checkbox" name="frm[2]"
                               value="3" <?php if ((isset($_GET['frm'][2]) && $_GET['frm'][2] == 3) || !isset($_GET['frm'])) {
                            echo 'checked="checked"';
                        } ?> class="frm_ch"/> <span class="l-radio">Сологуб</span>
                        <input type="checkbox" name="frm[3]"
                               value="4" <?php if ((isset($_GET['frm'][3]) && $_GET['frm'][3] == 4) || !isset($_GET['frm'])) {
                            echo 'checked="checked"';
                        } ?> class="frm_ch"/> <span class="l-radio">baza-broker</span>

                    </div>
                    <!-- <div class="pull-right" style="margin-right: 50px; margin-top: -4px">
					 	<span style="text-align:center; font-size:16px; font-weight:100">Выбран</span> <span style="text-align:center; font-size:18px; font-weight:500"><?php if (isset($_GET['fl_city'])) {
                        echo $_GET['fl_city'];
                    } else {
                        echo "Днепропетровск";
                    } ?></span>
					 </div>-->
                   <!-- <div class="pull-right" style="margin-right: 10px;margin-top: -4px">
                        Фильтр:
                    </div>-->
                </legend>

                <div id="fieldset_form">
                    <input type="hidden" name="op" value="<?php echo $op; ?>"/>
                    <input type="hidden" name="page" id="curr_page" value="<?php echo $page_c; ?>"/>
                    <input type="hidden" name="pager_active" id="pager_active" value="false"/>
                </div>
                    
                </div>


            </div>

            <div style="padding 0px; display:none; margin: 0px; height: 200px; visibility: hidden; overflow:hidden" id="di_d">
                <?php
                $op_i = isset($_GET['op']) ? $_GET['op'] : 1;
                foreach ($districts[$op_i] as $dis) :
                    if (isset($_GET['fl_mic']) && in_array($dis, $_GET['fl_mic'])) {
                        echo '<input type="checkbox" name="fl_mic[]" id="fl_mic" value="' . $dis . '" checked="checked">' . $dis . '<br>';
                    } else {
                        echo '<input type="checkbox" name="fl_mic[]" id="fl_mic" value="' . $dis . '">' . $dis . '<br>';
                    }
                endforeach;
                ?>
            </div>

            <div style="padding 0px; display:none; margin: 0px; height: 0px; visibility: hidden;overflow:hidden" id="fl_d">
                <div>
                    <div style="float:left">
                        Этаж от <input name="fl_floor_from" class="form-control input-sm" type="text" placeholder="от"
                                       value="<?php if (isset($_GET['fl_floor_from'])) echo($_GET['fl_floor_from']); ?>"/>
                    </div>
                    <div style="float:left">
                        до <input name="fl_floor_to" class="form-control input-sm" type="text" placeholder="до"
                                  value="<?php if (isset($_GET['fl_floor_to'])) echo($_GET['fl_floor_to']); ?>"/>
                    </div>
                </div>

                <div>
                    <div style="float:left">
                        Этажность от <input name="fl_floor_all_from" class="form-control input-sm" type="text"
                                            placeholder="от"
                                            value="<?php if (isset($_GET['fl_floor_all_from'])) echo($_GET['fl_floor_all_from']); ?>"/>
                    </div>
                    <div style="float:left; padding-bottom:5px">
                        до <input name="fl_floor_all_to" class="form-control input-sm" type="text" placeholder="до"
                                  value="<?php if (isset($_GET['fl_floor_all_to'])) echo($_GET['fl_floor_all_to']); ?>"/>
                    </div>
                </div>
            </div>

            <div style="padding 0px; display:none; margin: 0px; height: 0px; visibility: hidden;overflow:hidden" id="st_d">
                Укажите часть названия улицы
                <input name="fl_street" class="form-control input-sm" type="text" placeholder="Улица"
                       value="<?php if (isset($_GET['fl_street'])) echo($_GET['fl_street']); ?>"/>

            </div>


            <div style="padding 0px; display:none; margin: 0px; height: 0px; visibility: hidden;overflow:hidden" id="uid_d">
                Укажите цифри кода
                <input name="uid" class="form-control input-sm" type="text" placeholder="Улица"
                       value="<?php if (isset($_GET['uid'])) echo($_GET['uid']); ?>"/>

            </div>
            <div style="padding 0px; display:none; margin: 0px; height: 100px; visibility: hidden; overflow: hidden" id="ty_d">
                <?php
                $op_i = isset($_GET['op']) ? $_GET['op'] : 1;

                foreach ($estateType[$op_i] as $estT) :
                    if (isset($_GET['fl_type']) && in_array($estT, $_GET['fl_type'])) {
                        echo '<input type="checkbox" name="fl_type[]"  value="' . $estT . '" checked="checked">' . $estT . '<br>';
                    } else {
                        echo '<input type="checkbox" name="fl_type[]"  value="' . $estT . '">' . $estT . '<br>';
                    }
                endforeach;
                ?>
                <!--  <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Комната', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Комната">Комната<br>
							  <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Квартира в новострое', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Квартира в новострое">Квартира в новострое<br>
							  <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Квартира', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Квартира">Квартира<br>
							  <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Часть дома', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Часть дома">Часть дома<br>
							  <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Дом', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Дом">Дом<br>
							  <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Комната с подсе', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Комната с подсе">Комната с подcелением<br>
							  <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Квартира-суточн', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Квартира-суточн">Квартира-суточн<br>
							  <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Дача', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Дача">Дача<br>
							 <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Гараж', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Гараж">Гараж<br>
							 <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Офис(помещ,здание)', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Офис(помещ,здание)">Офис(помещ,здание)<br>
							 <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Склад(производ,база)', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Склад(производ,база)">Склад(производ,база)<br>
							 <input type="checkbox" name="fl_type[]" <?php if (isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Киоск(павильон)', $arr_fl_type1)) {
                    echo "checked";
                } ?> value="Киоск(павильон)">Киоск(павильон)<br>-->
            </div>

            <div style="padding:0px; display:none; display: none; margin: 0px; height: 0px; visibility: hidden; overflow:hidden" id="metka_d">
                <?php
                for ($i = 0; $i <= 8; $i++) {
                    $metka = $i;
                    if (isset($_GET['fl_metka']) && in_array($metka, $_GET['fl_metka'])) {
                        echo '<input type="checkbox" name="fl_metka[]" id="fl_metka" value="' . $metka . '" checked="checked">' . getFlag($metka) . '<br>';
                    } else {
                        echo '<input type="checkbox" name="fl_metka[]" id="fl_metka" value="' . $metka . '">' . getFlag($metka) . '<br>';
                    }
                }
                ?>
            </div>

            <div style="padding:0px; display:none; display: none; margin: 0px; height: 0px; visibility: hidden;overflow:hidden" id="ro_d">
                Кол-во комнат <br>

                <input type="checkbox" class="" name="fl_rooms[1]"
                       value="1" <?php if ((isset($_GET['fl_rooms'][1]) && $_GET['fl_rooms'][1] == 1)) {
                    echo 'checked="checked"';
                } ?> /> 1 <br>
                <input type="checkbox" name="fl_rooms[2]"
                       value="2" <?php if ((isset($_GET['fl_rooms'][2]) && $_GET['fl_rooms'][2] == 2)) {
                    echo 'checked="checked"';
                } ?> /> 2 <br>
                <input type="checkbox" name="fl_rooms[3]"
                       value="3" <?php if ((isset($_GET['fl_rooms'][3]) && $_GET['fl_rooms'][3] == 3)) {
                    echo 'checked="checked"';
                } ?> /> 3 <br>
                <input type="checkbox" name="fl_rooms[4]"
                       value="4" <?php if ((isset($_GET['fl_rooms'][4]) && $_GET['fl_rooms'][4] == 4)) {
                    echo 'checked="checked"';
                } ?> /> 4 <br>
                <input type="checkbox" name="fl_rooms[5]"
                       value="5" <?php if ((isset($_GET['fl_rooms'][5]) && $_GET['fl_rooms'][5] == 5)) {
                    echo 'checked="checked"';
                } ?> /> 5 <br>
                <input type="checkbox" name="fl_rooms[6]"
                       value="6" <?php if ((isset($_GET['fl_rooms'][6]) && $_GET['fl_rooms'][6] == 6)) {
                    echo 'checked="checked"';
                } ?> /> больше 5

            </div>

            <div style="padding:0px; display:none; display: none; margin: 0px; height: 0px; visibility: hidden;overflow:hidden" id="ar_d">
                Площадь от
                <input name="area_from" class="form-control input-sm" type="text" placeholder="от"
                       value="<?php if (isset($_GET['area_from'])) echo($_GET['area_from']); ?>"/>
                до
                <input name="area_to" class="form-control input-sm" type="text" placeholder="до"
                       value="<?php if (isset($_GET['area_to'])) echo($_GET['area_to']); ?>"/>
            </div>


            <div style="padding:0px; display: none; margin:0px; height: 0px; visibility: hidden;overflow:hidden" id="pr_d">
                Цена от
                <input name="fl_price_from" class="form-control input-sm" type="text" placeholder="мин"
                       value="<?php if (isset($_GET['fl_price_from'])) echo($_GET['fl_price_from']); ?>"/>
                Цена до
                <input name="fl_price_to" class="form-control input-sm" type="text" placeholder="макс"
                       value="<?php if (isset($_GET['fl_price_to'])) echo($_GET['fl_price_to']); ?>"/>
            </div>

            <div style="padding:5px; display:none; margin:0px;height:0px;visibility:hidden;overflow:hidden" id="ph_d">
                Укажите цифры телефона
                <input name="fl_phone" class="form-control" type="text" placeholder="Телефон"
                       value="<?php if (isset($_GET['fl_phone'])) echo($_GET['fl_phone']); ?>"/>
            </div>
            <div style="padding 0px; display:none; margin: 0px; height: 0px; visibility: hidden;overflow:hidden" id="date_d">
                <input type="radio" name="fl_date_r"
                       value="fl_date_today" <?php if ((isset($_GET['fl_date_r']) && $_GET['fl_date_r'] == 'fl_date_today')) {
                    echo 'checked="checked"';
                } ?> /> За сегодня<br>
                <input type="radio" name="fl_date_r"
                       value="fl_date_yesterday" <?php if ((isset($_GET['fl_date_r']) && $_GET['fl_date_r'] == 'fl_date_yesterday')) {
                    echo 'checked="checked"';
                } ?> /> За вчера<br>
                <input type="radio" name="fl_date_r"
                       value="fl_date_week" <?php if ((isset($_GET['fl_date_r']) && $_GET['fl_date_r'] == 'fl_date_week')) {
                    echo 'checked="checked"';
                } ?> /> За неделю<br>
                <input type="radio" name="fl_date_r"
                       value="fl_date_month" <?php if ((isset($_GET['fl_date_r']) && $_GET['fl_date_r'] == 'fl_date_month')) {
                    echo 'checked="checked"';
                } ?> /> За месяц<br>
                <input type="radio" name="fl_date_r"
                       value="fl_date_kvartal" <?php if ((isset($_GET['fl_date_r']) && $_GET['fl_date_r'] == 'fl_date_kvartal')) {
                    echo 'checked="checked"';
                } ?> /> За квартал<br>
                <input type="radio" name="fl_date_r"
                       value="fl_date_halfyear" <?php if ((isset($_GET['fl_date_r']) && $_GET['fl_date_r'] == 'fl_date_halfyear')) {
                    echo 'checked="checked"';
                } ?> /> За полгода<br>
                <input type="radio" name="fl_date_r"
                       value="fl_date_year" <?php if ((isset($_GET['fl_date_r']) && $_GET['fl_date_r'] == 'fl_date_year')) {
                    echo 'checked="checked"';
                } ?> /> За год<br>
                <input type="radio" name="fl_date_r"
                       value="fl_date_all" <?php if ((isset($_GET['fl_date_r']) && $_GET['fl_date_r'] == 'fl_date_all')) {
                    echo 'checked="checked"';
                } ?> /> За все время<br>
                <br>За период с<br>
                <input name="fl_date_from" class="form-control input-sm" type="text"
                       placeholder="Нажмите, чтобы выбрать" id="datepicker1"
                       value="<?php if (isset($_GET['fl_date_from'])) echo($_GET['fl_date_from']); ?>"/>
                По<br>
                <input name="fl_date_to" class="form-control input-sm" type="text" placeholder="Нажмите, чтобы выбрать"
                       id="datepicker2" value="<?php if (isset($_GET['fl_date_to'])) echo($_GET['fl_date_to']); ?>"/>
                <?php
                $months = array('1' => 'Январь', '2' => 'Февраль', '3' => 'Март', '4' => 'Апрель', '5' => 'Май', '6' => 'Июнь', '7' => 'Июль', '8' => 'Август', '9' => 'Сентябрь', '10' => 'Октябрь', '11' => 'Ноябрь', '12' => 'Декабрь');
                $present = date_parse(date('Y-m-d'));
                $oldest = date_parse('2014-09-01');
                ?>
                Календарный месяц<br>
                <div style="height: 200px; overflow:scroll">
                    <?php
                    $year_dif = $present['year'] - $oldest['year'] + 1;
                    $y = $present['year'];
                    $m = $present['month'];
                    $mark = 'go';
                    $index = 0;
                    while ($mark == 'go') {
                        ?>
                        <input type="checkbox" name="fl_date_range[<?= $index ?>]"
                               value="<?= $y . "-" . $m . "-01" ?>" <?php if ((isset($_GET['fl_date_range'][$index]) && $_GET['fl_date_range'][$index] == $y . "-" . $m . "-01")) {
                            echo 'checked="checked"';
                        } ?> /> <?= $months[$m] . " " . $y ?> <br>
                        <?php

                        if ($y == $oldest['year'] && $m == $oldest['month']) {
                            $mark = 'break';
                            break;
                        }

                        if ($m > 1) {
                            $m--;
                        } else {
                            $y--;
                            $m = 12;
                        }

                        $index++;
                    }
                    ?>
                </div>

            </div>
        </form>
    </div>
    <table class="table table-striped table-bordered table-soft" style="margin-top:10px;">
    <thead>
    <tr>
        <!--<th id="uid_th" class="fil <?php if (isset($_GET['uid']) && !empty($_GET['uid'])) {
            echo "bot_border";
        } ?>" data-attr="uid_d">Код</th>-->
        <th id="di_th" class="fil <?php if (isset($_GET['fl_mic']) && !empty($_GET['fl_mic'])) {
            echo "bot_border";
        } ?>" data-attr="di_d">Микрорайон
        </th>
        <th id="st_th" class="fil <?php if (isset($_GET['fl_street']) && !empty($_GET['fl_street'])) {
            echo "bot_border";
        } ?>" data-attr="st_d">Улица
        </th>
        <th id="ty_th" class="fil <?php if (isset($_GET['fl_type']) && !empty($_GET['fl_type'])) {
            echo "bot_border";
        } ?>" data-attr="ty_d">Тип
        </th>
        <th id="ar_th"
            class="fil <?php if ((isset($_GET['area_from']) && !empty($_GET['area_from'])) || (isset($_GET['area_to']) && !empty($_GET['area_to']))) {
                echo "bot_border";
            } ?>" data-attr="ar_d">Площадь
        </th>
        <th id="fl_th"
            class="fil <?php if ((isset($_GET['fl_floor_from']) && !empty($_GET['fl_floor_from'])) || (isset($_GET['fl_floor_to']) && !empty($_GET['fl_floor_to'])) || (isset($_GET['fl_floor_all_from']) && !empty($_GET['fl_floor_all_from'])) || (isset($_GET['fl_floor_all_to']) && !empty($_GET['fl_floor_all_to']))) {
                echo "bot_border";
            } ?>" data-attr="fl_d" width="60px">Этаж
        </th>
        <th id="ro_th" class="fil <?php if (isset($_GET['fl_rooms']) && !empty($_GET['fl_rooms'])) {
            echo "bot_border";
        } ?>" data-attr="ro_d">Комн
        </th>
        <th id="pr_th"
            class="fil <?php if ((isset($_GET['fl_price_from']) && !empty($_GET['fl_price_from'])) || (isset($_GET['fl_price_to']) && !empty($_GET['fl_price_to']))) {
                echo "bot_border";
            } ?>" data-attr="pr_d">Цена
        </th>
        <th id="ph_th" class="fil <?php if (isset($_GET['fl_phone']) && !empty($_GET['fl_phone'])) {
            echo "bot_border";
        } ?>" data-attr="ph_d">Телефон
        </th>
        <th id="date_th"
            class="fil <?php if ((isset($_GET['fl_date_from']) && !empty($_GET['fl_date_from'])) || (isset($_GET['fl_date_to']) && !empty($_GET['fl_date_to'])) || (isset($_GET['fl_date_r']) && !empty($_GET['fl_date_r']))) {
                echo "bot_border";
            } ?>" data-attr="date_d">Дата
        </th>
        <th id="metka_th" class="fil <?php if (isset($_GET['fl_metka']) && !empty($_GET['fl_metka'])) {
            echo "bot_border";
        } ?>" data-attr="metka_d">Метка
        </th>
        <th>Ссылка</th>
        <th>Дополнительно</th>
    </tr>
    </thead>
    <tbody id="sellsbody">
<?php }
foreach ($offers as $offer) {
    # code...
    $usr_id = str_replace("user","", $_COOKIE['bd_usr']);?>
    <tr id="<?php echo $offer['id_mn']; ?>">
        <!-- <td><?php echo $offer['region'] ?></td> -->
        <!--  <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php echo $offer['id_mn']; ?></td>-->
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php if ($_GET['fl_city'] == "Пригород") {
                echo $offer['city'] . " / ";
            } else {
                echo $offer['district'];
            } ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php echo $offer['street']; ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php echo $offer['estateType']; ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php echo $offer['area_json']; ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php echo $offer['floor']; ?> / <?php echo $offer['max_floor']; ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php echo $offer['rooms']; ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php if ($offer['price']) echo $offer['price'] . (($offer['operation'] == 1 || $offer['operation'] == 4) ? '$' : ' грн.'); else echo '---'; ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><p class="pn"><?php echo $offer['phones']; ?></p></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php echo date("d/m/Y H:i:s", $offer['adDate']); ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php echo getFlag($offer['flags']); ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><?php if ($offer['flags'] == 6 || $offer['flags'] == 8) echo "<span>R</span>"; ?>
            <?php if (!preg_match('/kg-r/', $offer['url']) && $offer['url']) { ?><a href="<?php echo $offer['url']; ?>"
                                                                                    target="_blank">
                    Источник</a><?php } ?></td>
        <td class=" <?php if($usr_id != $offer['user_id']) {
            echo 'unwatched';
        }  else echo 'watched';?>"><a href="#"
                 onClick="showmore(<?php echo $offer['id_mn']; ?>);return false;">Описание <?php if (!empty($offer['idmn'])) {
                    echo "<strong style='color:#ff382e'>+ К</strong>";
                } ?></a>
            <div style="display:none;" id='t<?php echo $offer['id_mn']; ?>' data-imageid="<?= ($offer['images']); ?>">
                <h4 style="display:none;"><?php echo typeop($offer['operation']); ?></h4>
                <i class="pull-right"
                   style="color: grey;">База:<?php if ($offer['frm'] == 2) echo("ray2"); elseif ($offer['frm'] == 1) echo("квадрат");
                    elseif ($offer['frm'] == 3) echo("сологуб");
                    elseif ($offer['frm'] == 4) echo("baza-broker"); ?></i>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td><b>Цена:</b></td>
                        <td><?php if ($offer['price']) echo $offer['price'] . (($offer['operation'] == 1 || $offer['operation'] == 4) ? '$' : ' грн.'); else echo '---'; ?></td>
                    </tr>
                    <tr>
                        <td><b>Адрес:</b></td>
                        <td><?php echo $offer['street']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Комнат:</b></td>
                        <td><?php echo $offer['rooms']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Телефон(ы):</b></td>
                        <td><?php echo $offer['phones']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Статус:</b></td>
                        <td><?php $status = $offer['is_watched'] == 1 ? 'Просмотрено ' . $offer['time_watched'] : "Новая запись";
                            echo $status; ?></td>
                    </tr>
                    <!-- <tr><td><b></b></td><td></td></tr> -->
                </table>
                <h4>Описание:</h4>
                <p style="word-wrap: break-word;"><?php echo $offer['text'] ?></p>
                <div class="comments" style="display:none;">
                    <h4>Комментарии:</h4>
                </div>

            </div>
        </td>
    </tr>

<?php }
if (!$ajax) { ?>


    </tbody>

    </table>
<?php
$first_pg = (($page_c + 16) <= $pages_pag) ? $page_c : $pages_pag - 16;
$last_pg = (($page_c + 16) <= $pages_pag) ? ($page_c + 16) : $pages_pag;
if ($pages_pag < 16) {
    $first_pg = 0;
    $last_pg = $pages_pag;
}
?>

    <div id="paginator" class="row-centered">
        <div class="col-centered">
            <ul class="nav nav-pills">
                <li><input type="button" id="first_p" data-attr="0" value="Первая"
                           class="paginator btn <?php if ($page_c == 0) {
                               echo "btn-primary";
                           } else {
                               echo "btn-default";
                           } ?>" <?php if ($page_c == $pages_pag) {
                        echo "disabled";
                    } ?> <?php if ($page_c == 0) {
                        echo "disabled";
                    } ?>></li>
                <li><input type="button" id="prev_page" data-attr="<?= $page_c - 16 ?>" value="Назад"
                           class="paginator btn btn-default" <?php if ($page_c <= 16) {
                        echo "disabled";
                    } ?>></li>
                <?php for ($i = $first_pg; $i < $last_pg; $i++) : ?>
                    <li><input type="button" value="<?= $i + 1 ?>" data-attr="<?= $i ?>"
                               class="paginator btn <?php if ($page_c == $i) {
                                   echo "btn-primary";
                               } else {
                                   echo "btn-default";
                               } ?>"></li>
                <?php endfor; ?>
                <li><input type="button" id="next_page" data-attr="<?= $page_c + 16 ?>" value="Дальше"
                           class="paginator btn btn-default" <?php if ($page_c >= ($pages_pag - 16)) {
                        echo "disabled";
                    } ?>></li>
                <li><input type="button" id="last_p" data-attr="<?= $pages_pag - 1 ?>"
                           value="Последняя (<?= $pages_pag ?>)"
                           class="paginator btn <?php if ($page_c == $pages_pag - 1) {
                               echo "btn-primary";
                           } else {
                               echo "btn-default";
                           } ?>" <?php if (($page_c == $pages_pag - 1) || ($pages_pag == 0)) {
                        echo "disabled";
                    } ?>></li>
            </ul>
        </div>
    </div>


    </div>
    </div>

    <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    <!-- <h5>Заголовок</h5> -->
                    <p id="ofdesc" data-id="0"></p>


                    <h4>Добавить комментарий:</h4>
                    <textarea id="txtcomment" class="form-control"></textarea>
                    <input class="form-control" type="submit" onClick="addmessage()" value="Оставить комментарий">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" id="statsModal" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                <div class="modal-body" align="center">
                    <h2 align="center" class="bg-back" style="height:50px!important">Статистика</h2>
                    <span align="center" class="col-main">(Обновлено <?php echo $stats[0]['time']; ?>)</span>
                    <table cellpadding="12" cellspacing="12" border="1" style="font-size:12px">
                        <tr align="center">
                            <th class="stats">Всего объектов в базе</th>
                            <th class="stats">Объекты по городах</th>
                            <th class="stats">Объекты по базах данных</th>
                            <th class="stats">Объекты по типу</th>
                            <th class="stats">Объекты по типу операции</th>
                        </tr>
                        <tr align="center" style="vertical-align:top">
                            <td><b><?php echo $stats[0]['stats_all']; ?></b></td>
                            <td>
                                <strong>Днепропетровск:</strong> <?php echo $stats[0]['stats_dnepropetrovsk']; ?><br>
                                <strong>Днепродзержинск:</strong> <?php echo $stats[0]['stats_dneprodzerzhinsk']; ?><br>
                                <strong>Новомосковск:</strong> <?php echo $stats[0]['stats_novomoskovsk']; ?>
                            </td>
                            <td>
                                <?php $stats_providers = unserialize($stats[0]['stats_providers']); ?>
                                <strong>Квадрат:</strong> <?php echo $stats_providers[0]['count']; ?><br>
                                <strong>Сологуб:</strong> <?php echo $stats_providers[2]['count']; ?><br>
                                <strong>Ray2:</strong> <?php echo $stats_providers[1]['count']; ?><br>
                                <strong>baza-broker:</strong> <?php echo $stats_providers[3]['count']; ?>
                            </td>
                            <td>
                                <?php $stats_types = unserialize($stats[0]['stats_flats']); ?>
                                <?php foreach ($stats_types as $index => $val) { ?>
                                    <?php if (!empty($val['estateType'])) { ?>
                                        <strong><?= $val['estateType']; ?>:</strong>  <?= $val['count']; ?><br>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                            <td>
                                <?php $stats_operations = unserialize($stats[0]['stats_operations']); ?>
                                <strong>Продам:</strong> <?php echo $stats_operations[1]['count']; ?><br>
                                <strong>Сдам:</strong> <?php echo $stats_operations[2]['count']; ?><br>
                                <strong>Сдам посуточно:</strong> <?php echo $stats_operations[3]['count']; ?><br>
                                <strong>Куплю:</strong> <?php echo $stats_operations[4]['count']; ?><br>
                                <strong>Сниму:</strong> <?php echo $stats_operations[5]['count']; ?><br>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" id="addEntryModal" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                <div class="modal-body" align="center">
                    <h2 align="center" class="bg-back" style="height:50px!important">Добавить в Эксклюзивы</h2>
                    <div>
                        <div class="float">
                            <a href="#" class="form-control active2 sell_tr b_r" data-href="sell_tr"
                               style="margin-top:3px">Продажа</a>
                        </div>
                        <div class="float">
                            <a href="#" class="form-control ren_tr b_r" data-href="ren_tr"
                               style="margin-top:3px">Аренда</a>
                        </div>
                    </div>
                    <br clear="all">
                    <form action="" id="add_entry_sell" enctype="multipart/form-data" method="get">
                        <table cellpadding="12" cellspacing="12" width="50%" border="1" style="font-size:12px">
                            <tr align="center">
                                Микрорайон
                                <input name="district" class="form-control" type="text"
                                       placeholder="Пример: 12-й квартал" value=""/>
                            </tr>
                            <tr align="center">
                                Улица
                                <input name="street" class="form-control" type="text"
                                       placeholder="Пример: Регенераторная, 4" value=""/>
                            </tr>
                            <tr align="center">
                                Тип
                                <input name="type" class="form-control" type="text" placeholder="Пример: Квартира"
                                       value=""/>
                            </tr>
                            <tr align="center">
                                Комнат
                                <input name="rooms" class="form-control" type="text" placeholder="Пример: 2" value=""/>
                            </tr>
                            <tr align="center">
                                Этаж
                                <input name="floor" class="form-control" type="text" placeholder="Пример: 2" value=""/>
                            </tr>
                            <tr align="center">
                                Этажность
                                <input name="max_floor" class="form-control" type="text" placeholder="Пример: 10"
                                       value=""/>
                            </tr>
                            <tr align="center">
                                Площадь
                                <input name="area" class="form-control" type="text" placeholder="Пример: 65" value=""/>
                            </tr>
                            <tr align="center">
                                Цена, у.е.
                                <input name="price" class="form-control" type="text" placeholder="Пример: 45000"
                                       value=""/>
                            </tr>
                            <tr align="center">
                                Телефон
                                <input name="phone" class="form-control" type="text" placeholder="" value=""/>
                            </tr>
                            <tr align="center">
                                ФИО менеджера
                                <input name="manager" class="form-control" type="text" placeholder="" value=""/>
                            </tr>
                            <tr align="center">
                                Комиссия от продажы
                                <input name="comission" class="form-control" type="text" placeholder="" value=""/>
                            </tr>
                            <tr align="center">
                                Ссылка
                                <input name="url" class="form-control" type="text" placeholder="" value=""/>
                            </tr>
                        </table>
                    </form>

                    <form action="" id="add_entry_rent" enctype="multipart/form-data" method="get" style="display:none">
                        <table cellpadding="12" cellspacing="12" width="50%" border="1" style="font-size:12px">
                            <tr align="center">
                                Дата
                                <input name="date" class="form-control" type="text" placeholder="26.10.2015" value=""/>
                            </tr>
                            <tr align="center">
                                Адрес/район
                                <input name="street" class="form-control" type="text"
                                       placeholder="Пример: Регенераторная, 4 / 12-ы квартал" value=""/>
                            </tr>
                            <tr align="center">
                                Тип
                                <input name="type" class="form-control" type="text" placeholder="Пример: Квартира"
                                       value=""/>
                            </tr>
                            <tr align="center">
                                Комнат
                                <input name="romms" class="form-control" type="text" placeholder="Пример: 2" value=""/>
                            </tr>
                            <tr align="center">
                                Цена, грн
                                <input name="price" class="form-control" type="text" placeholder="Пример: 2000"
                                       value=""/>
                            </tr>
                            <tr align="center">
                                ФИО менеджера
                                <input name="floor" class="form-control" type="text" placeholder="" value=""/>
                            </tr>
                            <tr align="center">
                                Описание
                                <input name="floor" class="form-control" type="text" placeholder="" value=""/>
                            </tr>
                            <tr align="center">
                                Примечание
                                <input name="floor" class="form-control" type="text" placeholder="" value=""/>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div id="linker"></div>
    <div id="universal"
         style="position:absolute;background: rgba(222,222,222,.9);border: 1px solid #cccccc;height:auto;width:auto;">

    </div>

    <script type="text/javascript">
        var photos_ARCHIVE_array = [];
        var active_ID = '';
        $(document).ready(function () {
            $('.paginator').click(function () {
                var page = $(this).attr('data-attr');
                $('#pager_active').val('true');
                $('#curr_page').val(page);
                $('#frm_filter').submit();
            });

            $('.reset-filter').click(function () {
                location.replace('/');
            });

            //$("#fl_type,#fl_mic").chosen();
            $("#ty_d,#di_d").css({
                'height': '0px',
                'visibility': 'hidden'
            });
            var initial = $("#ty_d,#di_d,#st_d,#uid_d,#fl_d").detach();
            $('#frm_filter').append(initial);

            var height_th = $('.table-striped th:first').height() + (2 * parseInt($('.table-striped th:first').css('padding-top')));
            //var filter = [{"fl_rooms": '5'}]; //alert(filter[0].fl_rooms);
            $('.frm_ch').on('click', function () {
                $('#frm_filter').submit();
            });

            $('.frm_ch1').on('click', function () {
                $('#frm_filter').submit();
                //location.replace('/?fl_city='+$(this).val());
            });

            $('#add_g_entry').on('click', function () {
                $('#addEntryModal').modal('show');
            });

            $('.b_r').on('click', function () {
                $('.b_r').removeClass('active2');
                $(this).addClass('active2');
                var id = $(this).attr('data-href');
                if (id == 'sell_tr') {
                    $('#add_entry_rent').hide();
                    $('#add_entry_sell').show();
                } else if (id == 'ren_tr') {
                    $('#add_entry_sell').hide();
                    $('#add_entry_rent').show();
                }
            });

            $('#frm_filter').mouseover(function () {
                var active_value = $('#universal > div').detach();
                $('#frm_filter').append(active_value);
                $(active_value).css({
                    'height': '0px',
                    'visibility': 'hidden'
                });
                $('#universal').hide();
            });


            $('#di_th, #date_th, #fl_th, #ro_th, #st_th, #ty_th, #ar_th, #pr_th, #ph_th, #uid_th, #metka_th').on('click', function () {

                var active_value = $('#universal > div').detach();
                $(active_value).css({
                    'height': '0px',
                    'visibility': 'hidden',
                    'margin': '0px',
                    'overflow': 'hidden'
                });
                $('#frm_filter').append(active_value);

                var id = $(this).attr('data-attr');

                if (id == 'di_d' || id == 'ty_d') {
                    var d_hei = $('.table-striped').offset();
                    var win_hei = $(window).height();
                    //alert(d_hei.top);
                    var html = $('#' + id).show().css({
                        'height': win_hei - 100 + 'px',
                        'visibility': 'visible',
                        'margin': '15px',
                        'overflow': 'scroll'
                    });
                } else if (id == 'ty_d') {
                    var html = $('#' + id).show().css({
                        'height': 'auto',
                        'visibility': 'visible',
                        'margin': '15px'
                    });
                } else {
                    var html = $('#' + id).show().css({
                        'height': 'auto',
                        'visibility': 'visible',
                        'margin': '15px',
                    });
                }

                var html = $(html).detach();
                $('#universal').html(html);
            });

            $('.save-filter').on('click', function () {

                if (!$('#name_filter').hasClass('active1')) {
                    $('#name_filter,#save_filter').addClass('active1');
                    $(this).val('Отменить сохранение');
                } else {
                    $('#name_filter,#save_filter').removeClass('active1');
                    $(this).val('Сохранить фильтр');
                }

                $('#name_filter,#save_filter').toggle();
            });

            $('#save_filter').on('click', function () {
                $('#name_filter,#save_filter').removeClass('active1').hide();
                $('.save-filter').val('Сохранить фильтр');
                SaveUrl();
                window.location.assign(window.location.pathname);
                $('#name_filter').val('Название фильтра');
            });

            $('.remove-filter').on('click', function () {
                RemoveUrl();
                window.location.assign(window.location.pathname);
            });

            $('#select_filter').on('change', function () {
                var url = $(this).val();
                var location = window.location;
                var options = $(this).find('option:selected');
                var id = $(options[0]).attr('data-id');
                window.location.assign(location.pathname + url + '&filter_id=' + id);
            });


            $('.fil').on('click', function () {
                var id = $(this).attr('id');
                var offset = $('#' + id).offset();
                var height = 1.5 * parseInt($('#' + id).css('height'));
                var width = 1.5 * parseInt($('#' + id).css('width'));
                $('#universal').css({
                    'top': (offset.top + height_th + 2) + 'px',
                    'left': offset.left + 'px',
                    'min-width': width + 'px',
                    'min-height': height + 'px',
                }).html();
                $('#universal').show();

            });

            $('#show_hide_f').on("click", function () {
                if ($(this).is(':checked') == true) {
                    $('#fieldset_form').show();
                } else {
                    $('#fieldset_form').hide();
                }
            });


            $("#datepicker1,#datepicker2").datepicker({
                changeMonth: true,
                changeYear: true,
            });

            $("#datepicker1").datepicker("option",
                $.datepicker.regional["ru"]);
            $("#datepicker2").datepicker("option",
                $.datepicker.regional["ru"]);

        });

        function checkUrl() {
            var location = window.location;
            var filter_id = "<?php if (isset($_GET['filter_id'])) {
                echo $_GET['filter_id'];
            } else {
                echo '';
            }?>";
            if (location.search != '' && filter_id == '') {
                $('.save-filter').removeAttr('disabled');
            }
            console.log(location);
        }
        checkUrl();

        function SaveUrl() {
            var location = window.location;
            if (location.search != '') {
                var uid = <?php echo str_replace("user","", $_COOKIE['bd_usr']);?>;
                var query = location.search;
                var name = $('#name_filter').val();
                var data = {'mode': 'saveurl', 'query': query, 'name': name, 'userid':uid};
                jQuery.ajax({
                    type: 'POST',
                    url: "parser/classes.php",
                    data: data,
                    dataType: 'html',
                    complete: function (data) {

                    },
                    success: function(msg){
                        //alert(msg);
                    },
                    error: function(msg){
                        //alert(msg);
                    }
                });
            }
            console.log(location);
        }

        function RemoveUrl() {
            var location = window.location;
            if (location.search != '') {
                var query = location.search;
                var name = $('#name_filter').val();
                var id = $('#r_filter').attr('data-id');
                var data = {'mode': 'removeurl', 'id': id};
                jQuery.ajax({
                    type: 'POST',
                    url: "parser/classes.php",
                    data: data,
                    dataType: 'html',
                    complete: function (data) {

                    }
                });
            }
            console.log(location);
        }

        function showStats() {
            $('#statsModal').modal('show');
        }

        function showmore(id) {
            //$('#t'+id).show();
            var user_id = <?php echo str_replace("user", "", $_COOKIE['bd_usr']);?>;
            $('#' + id + ' td').removeClass('unwatched').addClass('watched');
            $('#ofdesc').data('id', id).attr('data-id', id);
            $('#ofdesc').html($('#t' + id).html());
            $('#myModalLabel').html($('#t' + id + ' h4').eq(0).html());
            $('#photos_div').html('');
            var images = $('#t' + id).attr('data-imageid');
            var html = '<div id="photos_div">';
            if (images != '') {
                var im_arr = images.split(':::');
                for (var i = 0; i < im_arr.length; i++) {
                    var linkp = '<a class="fancybox" rel="group" href="' + im_arr[i] + '"><img src="' + im_arr[i] + '" height="100px" style="border:1px solid #fff; margin: 1px;"></a>';
                    html += linkp;
                }
            }
            if (images != '') {
                html += '<br><br><input type="button" onClick="getPhotoArchive()" value="Cкачать фотографии архивом"><br><br>';
            }
            html += '</div>';
            $('#ofdesc').after(html);
            $(".fancybox").fancybox();

            $('#myModal').modal('show');

            var cmn = $('#t' + id + ' .comments');
            if (cmn.css('display') == 'none')
                $.get("comments.php", {id: id}).done(function (data) {
                    // alert( "Data Loaded: " + data );
                    for (i in data) {
                        cmn.append("<p style=\"color:grey;\">" + data[i].author + ' ' + data[i].date + "</p>")
                        cmn.append("<p>" + data[i].message + "</p>")
                    }
                    if (data.length) {
                        cmn.show();
                        $('#ofdesc').html($('#t' + id).html());
                    }

                    active_ID = id;
                    photos_ARCHIVE_array = im_arr;

                    var data = {'id': id, 'mode': 'watched', 'user_id': user_id};
                    jQuery.ajax({
                        type: 'POST',
                        url: "parser/classes.php",
                        data: data,
                        dataType: 'html',
                        complete: function (data) {

                        }
                    });
                    // console.log(data);
                });
        }

        function getPhotoArchive() {
            var arrayed = encodeURI(photos_ARCHIVE_array);
            location.href = 'parser/classes.php?id=' + active_ID + '&action=archive&array=' + arrayed;
            console.log(arrayed);
        }


        function addmessage() {
            var id = $('#ofdesc').data('id');
            var message = $('#txtcomment').val();
            // console.log(message);
            $.get("comments.php", {id: id, message: message}).done(function (data) {
                console.log('DONE!!!');
                $('#txtcomment').val('');
                var cmn = $('#t' + id + ' .comments');
                cmn.append("<p style=\"color:grey;\">Вы только что</p>")
                cmn.append("<p>" + message + "</p>");
                cmn.show();
                $('#ofdesc').html($('#t' + id).html());
            });
        }
        var page = 0;
        function more() {
            if (page < 0) return;
            page++;
            $.get('index.php?' + ($('#frm_filter').serialize()) + '&ajax=true&page=' + page, function (done) {
                if (done != '') $('#sellsbody').append(done); else {
                    page = -1;
                    $('#loadmorebtn').hide();
                }
            });
        }
        function export_exel() {
            location.href = 'index.php?' + ($('#frm_filter').serialize()) + '&export=true';
        }

        function exporttoexcel() {
            // location.href='index.php?'+($('#frm_filter').serialize())+'&exportExcel=true';
            location.replace(location.href + '&exportExcel=true');
        }
    </script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!--<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>-->
    <script src="js/bootstrap.min.js"></script>
    </body>
    </html>
<?php } ?>