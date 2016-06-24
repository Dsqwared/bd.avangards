<?php
require("Db.class.php");
$db = new Db();

function get($params,$page=0){

	if (isset($params['operation'])) $op=$params['operation']; else $op=1; $db->bind("op",$op);
	
	$sql="SELECT * FROM parsed WHERE operation=:op ";

	

 if (isset($params['district']) && $params['district']!='') {
  $db->bind("fl_mic","%".$_POST['fl_mic']."%");
  $sql.=' AND district LIKE :fl_mic';
 }

  if (isset($_POST['fl_street']) && $_POST['fl_street']!='') {
  $db->bind("fl_street","%".$_POST['fl_street']."%");
  $sql.=' AND street LIKE :fl_street';
 }

  if (isset($_POST['fl_type']) && $_POST['fl_type']!='') {
  $db->bind("fl_type","%".$_POST['fl_type']."%");
  $sql.=' AND estateType LIKE :fl_type';
 }

 if (isset($_POST['fl_rooms']) && $_POST['fl_rooms']!='') {
  $db->bind("fl_rooms",$_POST['fl_rooms']);
  $sql.=' AND rooms=:fl_rooms';
 }

 if (isset($_POST['fl_price']) && $_POST['fl_price']!='') {

  if (preg_match('/(\d+)/', $_POST['fl_price'],$matches)) $prc=$matches[1]; else $prc=0;

  // var_dump($_POST['fl_price'][0]);

  switch ($_POST['fl_price'][0]) {
    case '>': $oper='>'; break;
    case '<': $oper='<'; break;
    default:$oper='='; break;
  }

  $sql.=" AND price${oper}${prc}";
 }

 if (isset($_POST['fl_phone']) && $_POST['fl_phone']!='') {
  $db->bind("fl_phone","%".$_POST['fl_phone']."%");
  $sql.=' AND phones LIKE :fl_phone';
 }

 if (isset($_POST['fl_date']) && $_POST['fl_date']!='') {
  $db->bind("fl_date", strtotime($_POST['fl_date']));
  $sql.=' AND adDate<:fl_date';
 }

  // var_dump($sql);
 $offers = $db->query($sql." ORDER BY adDate DESC LIMIT 0,50");



}


?>