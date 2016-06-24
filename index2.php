<?php 

function typeop($id){
 switch ($id) {
 	case 1: return 'Продам';
 	case 2: return 'Сдам';
 	case 3: return 'Сдам посуточно';
 	case 4: return 'Куплю';
 	case 5: return 'Сниму';
 	default: return '---';
 }
}

function export($list){
  
  $fp = fopen('export.csv', 'w');
  fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
  foreach ($list as $fields) {
      fputcsv($fp, array_values($fields));
  }
  fclose($fp);
  header("Location: export.csv");
  exit();

}

require("Db.class.php");
$db = new Db();

if (file_exists('cache/districts')) {
	$districts=unserialize(file_get_contents('cache/districts'));
} else {
	$districts = $db->query("SELECT district from parsed GROUP BY district ORDER BY district");
	file_put_contents('cache/districts', serialize($districts));
}

if (file_exists('cache/streets')) {
	$streets=unserialize(file_get_contents('cache/streets'));
} else {
	$streets = $db->query("SELECT street from parsed GROUP BY street ORDER BY street");
	file_put_contents('cache/streets', serialize($streets));
}

$rooms=isset($_GET['rooms'])?$_GET['rooms']:array();

	$op=1;
	$sql="SELECT * FROM parsed WHERE operation=:op ";

	if (isset($_GET['op'])) $op=$_GET['op'];
	$db->bind("op",$op);

if (isset($_GET['districts'])){
	$sql.=" AND ( ";

	foreach ($_GET['districts'] as $key => $value) {
		// var_dump($value);

	  $db->bind("fl_mic$key","%".$value."%");
	  if ($key>0) $sql.=" OR ";
	  $sql.=" district LIKE :fl_mic$key";
	} 
	$sql.=" ) ";

}

if (isset($_GET['streets'])){
	$sql.=" AND ( ";

	foreach ($_GET['streets'] as $key => $value) {
		// var_dump($value);

	  $db->bind("fl_str$key","%".$value."%");
	  if ($key>0) $sql.=" OR ";
	  $sql.=" street LIKE :fl_str$key";
	} 
	$sql.=" ) ";

}

if (!empty($rooms)){
	$sql.=" AND ( ";
		$b_cond=false;
	if (in_array('0', $rooms)) { $sql.=" rooms=0 "; $b_cond=true; }
	if (in_array('5', $rooms)) { if ($b_cond) $sql.=" OR "; $sql.=" rooms>=5 "; $b_cond=true; }
		$ch_r = array_diff($rooms,['0','5']);
	if (!empty($ch_r)) {
		foreach ($ch_r as $key=>$value) {
			if ($key>0||$b_cond) $sql.=" OR ";
			$sql.=' rooms = '.$value;
		}
	}

	$sql.=" ) ";
}

  if (isset($_GET['fl_type']) && $_GET['fl_type']!='') {
  $db->bind("fl_type","%".$_GET['fl_type']."%");
  $sql.=' AND estateType LIKE :fl_type';
 }

 if (isset($_GET['pricemin']) && $_GET['pricemin']!='') {
  $db->bind("pricemin",$_GET['pricemin']);
  $sql.=' AND price >= :pricemin';
 }
 if (isset($_GET['pricemax']) && $_GET['pricemax']!='') {
  $db->bind("pricemax",$_GET['pricemax']);
  $sql.=' AND price <= :pricemax';
 }

 if (isset($_GET['fl_rooms']) && $_GET['fl_rooms']!='') {
  $db->bind("fl_rooms",$_GET['fl_rooms']);
  $sql.=' AND rooms=:fl_rooms';
 }

 if (isset($_GET['fl_phone']) && $_GET['fl_phone']!='') {
  $db->bind("fl_phone","%".$_GET['fl_phone']."%");
  $sql.=' AND phones LIKE :fl_phone';
 }

 if (isset($_GET['fl_date']) && $_GET['fl_date']!='') {
  $db->bind("fl_date", strtotime($_GET['fl_date']));
  $sql.=' AND adDate<:fl_date';
 }

 if (isset($_GET['fl_dp_m']) && isset($_GET['fl_dp_d']) && isset($_GET['fl_dp_y']) && 
 	$_GET['fl_dp_m']!='' && $_GET['fl_dp_d']!='' && $_GET['fl_dp_y']!='') {
  $db->bind("fl_dp", strtotime($_GET['fl_dp_d'].'-'.$_GET['fl_dp_m'].'-'.$_GET['fl_dp_y'].' 23:59'));
  $sql.=' AND adDate<:fl_dp';
 }

 if (isset($_GET['fl_ds_m']) && isset($_GET['fl_ds_d']) && isset($_GET['fl_ds_y']) && 
 	$_GET['fl_ds_m']!='' && $_GET['fl_ds_d']!='' && $_GET['fl_ds_y']!='') {
  $db->bind("fl_ds", strtotime($_GET['fl_ds_d'].'-'.$_GET['fl_ds_m'].'-'.$_GET['fl_ds_y'].' 00:00'));
  $sql.=' AND adDate>=:fl_ds';
 }

if (isset($_GET['export'])){ 
	$offers = $db->query($sql." ORDER BY adDate DESC");
	export($offers);
} else {

  $page=0;
  if (isset($_GET['page'])) $page=(int)$_GET['page'];
  $page*=50;
  $tp=$page+50;
	$offers = $db->query($sql." ORDER BY adDate DESC LIMIT $page,$tp");

}

 $ajax=false;
 if (isset($_GET['ajax'])) $ajax=true;

 if (!$ajax) {
 ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>База</title>
    <meta name="viewport" content="width=device-width">
<!-- <link href="http://www.kg-r.com/css/style.css?64" rel="stylesheet"> -->
     <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"><!-- Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css"><!-- Bootstrap -->
    <link href="style.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript">
    $(function(){

    	$('.base-filter-container').click(function(){
    		$('.base-filter-list-title').parent().parent().hide();
    		$(this).children('.base-filter').show();
    	});
    	
    	$('.base-filter-list-title').click(function(){
    		$(this).parent().parent().hide();
    	});
    	$(".base-ad").hover(
			  function () {
			    $(this).addClass('base-ad-selected');
			  }, 
			  function () {
			    $(this).removeClass('base-ad-selected');
			  }
		  );
    });
    </script>
</head>
<body>

<div style="wigth:100%;">
  		<img style="position: absolute; height:auto; left:0; right:0; top:0;bottom:0; margin: 0 auto;" src="http://avangards.com.ua/wp-content/uploads/2015/02/logo_avangard22.png">
</div>

<div class="container main-container w1120" id="main-container" style="margin-left: auto; margin-top:60px;">
<ul class="nav nav-pills pull-left">
	  <li <?php if ($op==1) echo 'class="active"' ?>><a href="?op=1">Продам</a></li>
	  <li <?php if ($op==2) echo 'class="active"' ?>><a href="?op=2">Сдам</a></li>
	  <li <?php if ($op==3) echo 'class="active"' ?>><a href="?op=3">Сдам посуточно</a></li>
	  <li <?php if ($op==4) echo 'class="active"' ?>><a href="?op=4">Куплю</a></li>
	  <li <?php if ($op==5) echo 'class="active"' ?>><a href="?op=5">Сниму</a></li>
	</ul>
  <div class="pull-right"><input class="form-control" type="submit" onClick="export_exel()" value="Експорт"></div>

<div id="app" style="margin-top: 60px;">
<div id="base-table">
	<div id="base-filters-container">
		<div class="base-filters">
			<div class="base-filters-hider-left">
			</div>
			<div class="base-filters-hider-top">
			</div>

		<form method="get" id="frm_filter">
	        <input type="hidden" name="op" value="<?php echo $op; ?>" />

			<div id="base-filter-district" class="base-cell base-filter-container" style="width: 127px;">
				<div class="base-cell-text" data-field="district">
					Микрорайон
				</div>
				<div class="b-i base-filter-status">
				</div>
				<div class="base-filter">
					<div class="base-filter-list" style="min-width: 127px">
						<div class="base-filter-list-title" style="width: 112px">
							Микрорайон
						</div>
						<div class="base-filter-list-title-hider" style="width: 128px">
						</div>
						<div class="base-filter-list-items">
							<?php foreach ($districts as $key => $value) {
								$value=$value['district']; if ($value=='') continue;
								$selected=in_array($value, (isset($_GET['districts'])?$_GET['districts']:[]));
							 ?>
								<div class="base-checkbox">
									<input name="districts[]" <?php if ($selected) echo 'checked'; ?> type="checkbox" value="<?php echo $value; ?>" >
									
									<div class="title">
										<?php echo $value; ?>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="base-filter-submit-container">
							
								<input class="base-filter-submit" type="submit" value="Ок">
							
							<div class="base-filter-clear">
								Очистить фильтр
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="base-filter-street" class="base-cell base-filter-container" style="width: 130px;">
				<div class="base-cell-text" data-field="street">
					Улица
				</div>
				<div class="b-i base-filter-status">
				</div>
				<div class="base-filter">
					<div class="base-filter-list" style="min-width: 127px">
						<div class="base-filter-list-title" style="width: 112px">
							Улица
						</div>
						<div class="base-filter-list-title-hider" style="width: 128px">
						</div>
						<div class="base-filter-list-items">
							<?php foreach ($streets as $key => $value) {
								$value=$value['street']; if ($value=='') continue;
							    $selected=in_array($value, (isset($_GET['streets'])?$_GET['streets']:[]));
							 ?>
								<div class="base-checkbox">
									<input name="streets[]" <?php if ($selected) echo 'checked'; ?> type="checkbox" value="<?php echo $value; ?>" >
									<div class="title">
										<?php echo $value; ?>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="base-filter-submit-container">
							<input class="base-filter-submit" type="submit" value="Ок">
							<div class="base-filter-clear">
								Очистить фильтр
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="base-filter-estateType" class="base-cell base-filter-container" style="width: 80px;">
				<div class="base-cell-text" data-field="estateType">
					Тип
				</div>
				<!-- <div class="b-i base-filter-status">
				</div>
				<div class="base-filter">
					<div class="base-filter-list" style="min-width: 80px">
						<div class="base-filter-list-title" style="width: 65px">
							Тип
						</div>
						<div class="base-filter-list-title-hider" style="width: 81px">
						</div>
						<div class="base-filter-list-items">
							<div class="base-checkbox">
								<input type="checkbox" value="0" data-field="null">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									–
								</div>
							</div>
							<div class="base-checkbox">
								<input type="checkbox" value="1" data-field="estateType">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									Квартира
								</div>
							</div>
							<div class="base-checkbox">
								<input type="checkbox" value="2" data-field="estateType">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									Квартира в новострое
								</div>
							</div>
							<div class="base-checkbox">
								<input type="checkbox" value="3" data-field="estateType">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									Комната
								</div>
							</div>
						</div>
						<div class="base-filter-submit-container">
							<div class="base-filter-submit">
								ОК
							</div>
							<div class="base-filter-clear">
								Очистить фильтр
							</div>
						</div>
					</div>
				</div> -->
			</div>
			<div id="base-filter-p1-p2" class="base-cell base-filter-container" style="width: 43px;">
				<div class="base-cell-text" data-field="p1-p2">
					Комн
				</div>
				<div class="b-i base-filter-status">
				</div>
				<div class="base-filter">
					<div class="base-filter-list" style="min-width: 63px">                
					<div class="base-filter-list-title" style="width: 29px">Комн</div>                
					<div class="base-filter-list-title-hider" style="width: 44px"></div>                
					<div class="base-filter-list-items" style="width: 142px;">                    
					<div class="base-checkbox">                        
						<input <?php if (in_array('0', $rooms)) echo 'checked'; ?> name="rooms[]" type="checkbox" value="0">                        
						<div class="input"><div></div></div>
						<div class="title">–</div>                    
					</div>                    
					<div class="base-checkbox">
						<input <?php if (in_array('1', $rooms)) echo 'checked'; ?> name="rooms[]" type="checkbox" value="1">
						<div class="input"><div></div></div>
						<div class="title">1 - комнатные</div>                    
					</div>                    
					<div class="base-checkbox">                        
						<input <?php if (in_array('2', $rooms)) echo 'checked'; ?> name="rooms[]" type="checkbox" value="2">                        
						<div class="input"><div></div></div>
						<div class="title">2 - комнатные</div>                    
					</div>                    
					<div class="base-checkbox">
						<input <?php if (in_array('3', $rooms)) echo 'checked'; ?> name="rooms[]" type="checkbox" value="3">
						<div class="input"><div></div></div>
						<div class="title">3 - комнатные</div>                    
					</div>                    
					<div class="base-checkbox">
						<input <?php if (in_array('4', $rooms)) echo 'checked'; ?> name="rooms[]" type="checkbox" value="4">
						<div class="input"><div></div></div>
						<div class="title">4 - комнатные</div>                    
					</div>                    
					<div class="base-checkbox">                        
						<input <?php if (in_array('5', $rooms)) echo 'checked'; ?> name="rooms[]" type="checkbox" value="5">                        
						<div class="input"><div></div></div>
						<div class="title">5 - к. и более</div>                    
					</div>                
					</div>                

					<div class="base-filter-submit-container">                    
						<input class="base-filter-submit" type="submit" value="Ок">                    
						<!-- <div class="base-filter-clear">Очистить фильтр</div>                 -->
					</div>            
					</div>
				</div>
			</div>
		<!-- 	<div id="base-filter-p3-p4" class="base-cell base-filter-container" style="width: 60px;">
				<div class="base-cell-text" data-field="p3-p4">
					Этаж
				</div>
				<div class="b-i base-filter-status">
				</div>
				<div class="base-filter">
				</div>
			</div>
			<div id="base-filter-p5-p6-p7" class="base-cell base-filter-container" style="width: 85px;">
				<div class="base-cell-text" data-field="p5-p6-p7">
					Площадь
				</div>
				<div class="b-i base-filter-status">
				</div>
				<div class="base-filter" style="display: none;">
					<div class="base-filter-list base-filter-range" style="min-width: 105px">
						<div class="base-filter-list-title" style="width:71px">
							 Площадь
						</div>
						<div class="base-filter-list-title-hider" style="width: 86px">
						</div>
						<div class="base-filter-range-items">
							<div class="base-range">
								<div class="title">
									Общая площадь, м²
								</div>
								<input placeholder="от" type="text">
								<div class="base-range-inc">
									<div class="up b-i">
									</div>
									<div class="down b-i">
									</div>
								</div>
								 — <input placeholder="до" type="text">
								<div class="base-range-inc">
									<div class="up b-i">
									</div>
									<div class="down b-i">
									</div>
								</div>
							</div>
							<div class="base-range">
								<div class="title">
									Жилая площадь, м²
								</div>
								<input placeholder="от" type="text">
								<div class="base-range-inc">
									<div class="up b-i">
									</div>
									<div class="down b-i">
									</div>
								</div>
								 — <input placeholder="до" type="text">
								<div class="base-range-inc">
									<div class="up b-i">
									</div>
									<div class="down b-i">
									</div>
								</div>
							</div>
							<div class="base-range">
								<div class="title">
									Площадь кухни, м²
								</div>
								<input placeholder="от" type="text">
								<div class="base-range-inc">
									<div class="up b-i">
									</div>
									<div class="down b-i">
									</div>
								</div>
								 — <input placeholder="до" type="text">
								<div class="base-range-inc">
									<div class="up b-i">
									</div>
									<div class="down b-i">
									</div>
								</div>
							</div>
						</div>
						<div class="base-filter-radio">
							<div class="base-radio base-radio-checked">
								<input type="radio" checked="checked" name="base-order-p5-p6-p7" value="0">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									Не сортировать
								</div>
							</div>
							<div class="base-radio">
								<input type="radio" name="base-order-p5-p6-p7" value="1">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									По возрастанию
								</div>
							</div>
							<div class="base-radio">
								<input type="radio" name="base-order-p5-p6-p7" value="-1">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									По убыванию
								</div>
							</div>
						</div>
						<div class="base-filter-empty">
							<div class="base-checkbox base-checkbox-checked">
								<input type="checkbox" checked="" value="1">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									Показать пустые
								</div>
							</div>
						</div>
						<div class="base-filter-submit-container">
							<div class="base-filter-submit">
								ОК
							</div>
							<div class="base-filter-clear">
								Очистить фильтр
							</div>
						</div>
					</div>
				</div>
			</div> -->
			
			<div id="base-filter-price" class="base-cell base-filter-container" style="width: 71px;">
				<div class="base-cell-text" data-field="price">
					Цена
				</div>
				<div class="b-i base-filter-status">
				</div>
				<div class="base-filter">
					<div class="base-filter-list base-filter-range" style="min-width: 91px">
						<div class="base-filter-list-title" style="width:57px">
							 Цена
						</div>
						<div class="base-filter-list-title-hider" style="width: 72px">
						</div>
						<div class="base-filter-range-items">
							<div class="base-range">
								<div class="title">
									Цена за весь объект
								</div>
								<input name="pricemin" placeholder="от" type="text" value="<?php if (isset($_GET['pricemin'])) echo($_GET['pricemin']); ?>">
							
								 — <input name="pricemax" placeholder="до" type="text" value="<?php if (isset($_GET['pricemax'])) echo($_GET['pricemax']); ?>">
								
							</div>
						</div>
						<div class="base-filter-radio">
							<!-- <div class="base-radio base-radio-checked">
								<input type="radio" checked="checked" name="base-order-price" value="0">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									Не сортировать
								</div>
							</div>
							<div class="base-radio">
								<input type="radio" name="base-order-price" value="1">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									По возрастанию
								</div>
							</div>
							<div class="base-radio">
								<input type="radio" name="base-order-price" value="-1">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									По убыванию
								</div>
							</div> -->
						</div>
						<!-- <div class="base-filter-empty">
							<div class="base-checkbox base-checkbox-checked">
								<input type="checkbox" checked="" value="1">
								<div class="input">
									<div>
									</div>
								</div>
								<div class="title">
									Показать пустые
								</div>
							</div>
						</div> -->
						<div class="base-filter-submit-container">
							<input class="base-filter-submit" type="submit" value="Ок">
							<div class="base-filter-clear">
								Очистить фильтр
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="base-filter-phones" class="base-cell base-filter-container" style="width: 78px;">
				<div class="base-cell-text" data-field="phones">
					Телефон
				</div>
				<div class="b-i base-filter-status">
				</div>
				<div class="base-filter">
					<div class="base-filter-list" style="min-width: 98px">
					             <div class="base-filter-list-title" style="width: 64px">
					                                 Телефон                
			                                 	</div>
	                                 	               <div class="base-filter-list-title-hider" style="width: 79px"></div>
	                                 	               <div class="base-filter-search">                    
	                                 	               <input name="fl_phone" maxlength="20" value="<?php echo (isset($_GET['fl_phone']))?$_GET['fl_phone']:''; ?>"> 
	                                 	                              </div> 
	                                 	                                             <div class="base-filter-submit-container">
	                                 	                                <input class="base-filter-submit" type="submit" value="Ок">
	                                 	                                                    <div class="base-filter-clear">Очистить фильтр</div>
	                                 	                                                                    </div>            
                                                                 </div>
				</div>
			</div>
			<div id="base-filter-date" class="base-cell base-filter-container" style="width: 59px;">
				<div class="base-cell-text" data-field="date">
					Дата
				</div>
				<div class="b-i base-filter-status">
				</div>
				<div class="base-filter">
				<div class="base-filter-list base-filter-date" style="min-width: 79px">
						<div class="base-filter-list-title" style="width: 45px">
							 Дата
						</div>
						<div class="base-filter-list-title-hider" style="width: 60px">
						</div>
						<div class="base-filter-radio">
						
							<div class="base-dp-container">
								<div>
									с
								</div>
								<div class="base-date-inputs">
									<input name="fl_ds_d" type="text" value="<?php echo (isset($_GET['fl_ds_d']))?$_GET['fl_ds_d']:''; ?>" class="base-date-input base-date-day" maxlength="2">.
									<input name="fl_ds_m" type="text" value="<?php echo (isset($_GET['fl_ds_m']))?$_GET['fl_ds_m']:''; ?>" class="base-date-input base-date-month" maxlength="2">.
									<input name="fl_ds_y" type="text" value="<?php echo (isset($_GET['fl_ds_y']))?$_GET['fl_ds_y']:''; ?>" class="base-date-input base-date-year" maxlength="4">
									<div class="b-i">
									</div>
								</div>
								<input type="text" class="base-dp-input base-date-from" tabindex="-1">
							</div>
							<div class="base-dp-container">
								<div>
									по
								</div>
								<div class="base-date-inputs">
									<input name="fl_dp_d" type="text" value="<?php echo (isset($_GET['fl_dp_d']))?$_GET['fl_dp_d']:''; ?>" class="base-date-input base-date-day" maxlength="2">.
									<input name="fl_dp_m" type="text" value="<?php echo (isset($_GET['fl_dp_m']))?$_GET['fl_dp_m']:''; ?>" class="base-date-input base-date-month" maxlength="2">.
									<input name="fl_dp_y" type="text" value="<?php echo (isset($_GET['fl_dp_y']))?$_GET['fl_dp_y']:''; ?>" class="base-date-input base-date-year" maxlength="4">
									<div class="b-i">
									</div>
								</div>
								<input type="text" class="base-dp-input base-date-to" tabindex="-1">
							</div>
						</div>
						
						<div class="base-filter-submit-container">
							<input class="base-filter-submit" type="submit" value="Ок">
							<div class="base-filter-clear">
								Очистить фильтр
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="base-filter-mark" class="base-cell base-filter-container" style="width: 44px;">
				<div class="base-cell-text" data-field="mark">
					Метка
				</div>
			</div>

			<div id="base-filter-id" class="base-cell base-filter-container" style="width: 421px;">
				<div class="base-cell-text" data-field="mark">
					Описание
				</div>
			</div>

		</form>

			<div class="base-filters-hider-right">
			</div>
		</div>
	</div>
	<div id="base-ads-container">
		<div id="sellsbody" class="base-ads-container">

		<?php } $odd_even=false;
		foreach ($offers as $offer) {
      	# code...
       ?>
			<div class="base-ad ">
				<div class="base-ad-row base-ad-<?php echo ($odd_even=!$odd_even)?'odd':'even'; ?>">
				
					<div class="base-cell" title="Микрорайон" data-field="district" style="width: 127px;">
						<div class="base-cell-text base-cell-text-left">
							<?php echo $offer['district']; ?>
						</div>
					</div>
					<div class="base-cell" title="Улица" data-field="street" style="width: 130px;">
						<div class="base-cell-text base-cell-text-left">
							<?php echo $offer['street']; ?>
						</div>
					</div>
					<div class="base-cell" title="Тип объекта" data-field="estateType" style="width: 80px;">
						<div class="base-cell-text base-cell-text-left">
							<?php echo $offer['estateType']; ?>
						</div>
					</div>
					<div class="base-cell" title="Комнат (раздельных)" data-field="p1-p2" style="width: 43px;">
						<div class="base-cell-text base-cell-text-center">
							<?php echo $offer['rooms']; ?>
						</div>
					</div>
					<!-- <div class="base-cell" title="Этаж/Этажность" data-field="p3-p4" style="width: 60px;">
						<div class="base-cell-text base-cell-text-center">
							<span class="base-cell-text-number">8</span>&nbsp;<span class="base-cell-slash">/</span>&nbsp;<span class="base-cell-text-number">8</span>
						</div>
					</div>
					<div class="base-cell" title="Площадь" data-field="p5-p6-p7" style="width: 85px;">
						<div class="base-cell-text base-cell-text-center">
							<span class="base-cell-text-number">65</span>&nbsp;<span class="base-cell-slash">/</span>&nbsp;<span class="base-cell-empty base-cell-empty-number">–</span>&nbsp;<span class="base-cell-slash">/</span>&nbsp;<span class="base-cell-text-number">9</span>
						</div>
					</div> -->
					<div class="base-cell" title="Цена" data-field="price" style="width: 71px;">
						<div class="base-cell-text base-cell-text-right">
							<div class="base-cell-text base-cell-text-right">
								<?php if ($offer['price']) echo $offer['price'].(($offer['operation']==1||$offer['operation']==4)?'$':' грн.'); else echo '---'; ?>
							</div>
						</div>
					</div>
					<div class="base-cell" title="Телефон" data-field="phones" style="width: 78px;">
						<div class="base-cell-text base-cell-text-center">
							<?php echo $offer['phones']; ?>
						</div>
					</div>
					<div class="base-cell" title="Дата" data-field="date" style="width: 59px;">
						<div class="base-cell-text base-cell-text-center">
							<?php echo date("d/m/Y H:i:s",$offer['adDate']); ?>
						</div>
					</div>
					<div class="base-cell" data-field="mark" style="width: 44px;">
						<div class="base-cell-text base-cell-text-center">
							<div class="base-mark base-mark-<?php echo $offer['flags'];?>" title="Без метки">
							</div>
						</div>
					</div>

					<div class="base-cell" data-field="mark" style="width: 419px;">
						<div class="base-cell-text base-cell-text-center">
							<p><?php echo $offer['text'] ?></p>
						</div>
					</div>
					
					<div class="base-ad-mark-new">
					</div>
					<div class="base-ad-opened">
					</div>
				</div>
			</div>
		<?php } if (!$ajax) { ?>
			<!-- <div class="base-ad base-ad-new base-ad-selected" style="margin-bottom: -1px;">
				<div class="base-ad-row base-ad-odd" style="width: 1070px;">
					<div class="base-cell" title="Административный район" data-field="adminDistrict" style="width: 126px;">
						<div class="base-cell-text base-cell-text-left">
							Ленинский
						</div>
					</div>
					<div class="base-cell" title="Микрорайон" data-field="district" style="width: 127px;">
						<div class="base-cell-text base-cell-text-left">
							Парус 1,2
						</div>
					</div>
					<div class="base-cell" title="Улица" data-field="street" style="width: 130px;">
						<div class="base-cell-text base-cell-text-left">
							Штурманский пер.,&nbsp;7
						</div>
					</div>
					<div class="base-cell" title="Тип объекта" data-field="estateType" style="width: 80px;">
						<div class="base-cell-text base-cell-text-left">
							Квартира
						</div>
					</div>
					<div class="base-cell" title="Комнат (раздельных)" data-field="p1-p2" style="width: 43px;">
						<div class="base-cell-text base-cell-text-center">
							3
						</div>
					</div>
					<div class="base-cell" title="Этаж/Этажность" data-field="p3-p4" style="width: 60px;">
						<div class="base-cell-text base-cell-text-center">
							<span class="base-cell-text-number">8</span>&nbsp;<span class="base-cell-slash">/</span>&nbsp;<span class="base-cell-text-number">9</span>
						</div>
					</div>
					<div class="base-cell" title="Площадь" data-field="p5-p6-p7" style="width: 85px;">
						<div class="base-cell-text base-cell-text-center">
							<span class="base-cell-text-number">65</span>&nbsp;<span class="base-cell-slash">/</span>&nbsp;<span class="base-cell-empty base-cell-empty-number">–</span>&nbsp;<span class="base-cell-slash">/</span>&nbsp;<span class="base-cell-text-number">9</span>
						</div>
					</div>
					<div class="base-cell" title="Материал" data-field="p20" style="width: 70px;">
						<div class="base-cell-text base-cell-text-left">
							Кирпич
						</div>
					</div>
					<div class="base-cell" title="Цена" data-field="price" style="width: 71px;">
						<div class="base-cell-text base-cell-text-right">
							55 000 $
						</div>
					</div>
					<div class="base-cell" title="Телефон" data-field="phones" style="width: 78px;">
						<div class="base-cell-text base-cell-text-center">
							0675600774
						</div>
					</div>
					<div class="base-cell" title="Дата" data-field="date" style="width: 59px;">
						<div class="base-cell-text base-cell-text-center">
							12:35
						</div>
					</div>
					<div class="base-cell" data-field="mark" style="width: 44px;">
						<div class="base-cell-text base-cell-text-center">
							<div class="base-mark base-mark-0" title="Без метки">
							</div>
						</div>
					</div>
					<div class="base-cell" title="Инфо" data-field="info" style="width: 43px;">
						<div class="base-cell-text base-cell-text-center">
							<div class="base-info base-info-1">
							</div>
						</div>
					</div>
					<div class="base-cell base-ad-open" title="Раскрыть" data-field="id" style="width: 26px;">
						<div class="b-i base-cell-text base-ad-open-button">
						</div>
					</div>
					<div class="base-ad-mark-new">
					</div>
					<div class="base-ad-opened">
					</div>
				</div>
			</div> -->
				
		</div>
	</div>
	<div id="base-show-more">
		<div class="base-show-more">
			<a id="loadmorebtn" href="#" onClick="more(); return false;" style="<?php if (count($offers)<49) echo 'display:none;'; ?>" class="trigger-false">Показать еще результаты</a>
			<span id="base-loader-small" style="display: none;"></span>
		</div>
	</div>
	<div id="base-loader" style="top: 109px; height: 1000px; z-index: 10; display: none;">
	</div>
</div>
</div>
</div>
<script type="text/javascript">

  page=0;
  function more(){
    if (page<0) return;
    page++;
    $.get('index2.php?'+($('#frm_filter').serialize())+'&ajax=true&page='+page,function(done){if (done!='') $('#sellsbody').append(done); else {page=-1;$('#loadmorebtn').hide();} });
  }
  function export_exel(){
    location.href='index2.php?'+($('#frm_filter').serialize())+'&export=true';
  }
</script>
</body>
</html>

<?php } ?>