<?php

include_once "checklogin.php";
include_once "district.php";

if (!checklogin()) { header("Location: login.php"); exit();}

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

	$op=1;
	$sql="SELECT * FROM parsed WHERE operation=:op ";

	if (isset($_GET['op'])) $op=$_GET['op'];
	$db->bind("op",$op);
	

 if (isset($_GET['fl_mic']) && $_GET['fl_mic']!='Выберите район') {
  //$db->bind("fl_mic","%".$_GET['fl_mic']."%");
  //$sql.=' AND district LIKE :fl_mic';
   $arr_fl_mic = $_GET['fl_mic'];
	 $arr_fl_mic1 =  $arr_fl_mic; 
		 if(count($arr_fl_mic) >= 1) {
			$fl_mic_ = array_shift($arr_fl_mic);
			//$db->bind("frm",$frm_);
			$sql.=" AND (district LIKE '%".$fl_mic_."%'";
			if(count($arr_fl_mic) >= 1) {
				 foreach($arr_fl_mic as $fl_mic_) {
					  //$db->bind("frm",$frm_);
					  $sql.=" OR district LIKE '%".$fl_mic_."%'";
				  }
			  }
			 $sql.=")";
		  }
 }

  if (isset($_GET['fl_street']) && $_GET['fl_street']!='') {
  $db->bind("fl_street","%".$_GET['fl_street']."%");
  $sql.=' AND street LIKE :fl_street';
 }

  /*if (isset($_GET['fl_type']) && $_GET['fl_type']!='') {
  //$db->bind("fl_type", );
  $t = addslashes($_GET['fl_type']);
  $sql.=' AND estateType = $t';
 }*/
//print_r($_GET['fl_type']);exit;
  if (isset($_GET['fl_type']) && is_array($_GET['fl_type'])) {
 
	 $arr_fl_type = $_GET['fl_type'];
	 $arr_fl_type1 =  $arr_fl_type; 
		 if(count($arr_fl_type) >= 1) {
			$fl_type_ = array_shift($arr_fl_type);
			//$db->bind("frm",$frm_);
			$sql.=" AND (estateType='$fl_type_'";
			if(count($arr_fl_type) >= 1) {
				 foreach($arr_fl_type as $fl_type_) {
					  //$db->bind("frm",$frm_);
					  $sql.=" OR estateType='$fl_type_'";
				  }
			  }
			 $sql.=")";
		  }
	 }

 /*if (isset($_GET['fl_rooms']) && $_GET['fl_rooms']!='') {
  $db->bind("fl_rooms",$_GET['fl_rooms']);
  $sql.=' AND rooms=:fl_rooms';
 }*/
 	if (isset($_GET['area_from']) || isset($_GET['area_to'])) {
		//$sql.=' AND area != ""';
	}
 
  if (isset($_GET['area_from']) && !empty($_GET['area_from'])) {
  		 //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
  		 $sql.=' AND area >= '.$_GET['area_from'];
  }
  
  if (isset($_GET['area_to']) && !empty($_GET['area_to'])) {
  		 //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
  		 $sql.=' AND area <= '.$_GET['area_to'];
  }
 
  if (isset($_GET['fl_floor_from']) && !empty($_GET['fl_floor_from'])) {
  		 //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
  		 $sql.=' AND floor >= '.$_GET['fl_floor_from'];
  }
  
  if (isset($_GET['fl_floor_to']) && !empty($_GET['fl_floor_to'])) {
  		 //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
  		 $sql.=' AND floor <= '.$_GET['fl_floor_to'];
  }
  
   if (isset($_GET['fl_price_from']) && !empty($_GET['fl_price_from'])) {
  		 //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
  		 $sql.=' AND price >= '.$_GET['fl_price_from'];
  }
  
  if (isset($_GET['fl_price_to']) && !empty($_GET['fl_price_to'])) {
  		 //$db->bind("fl_floor_from", addslashes($_GET['fl_floor_from']));
  		 $sql.=' AND price <= '.$_GET['fl_price_to'];
  }
 
  if (isset($_GET['fl_rooms']) && is_array($_GET['fl_rooms'])) {
	 $arr_fl_rooms = $_GET['fl_rooms'];
		 if(count($arr_fl_rooms) >= 1) {
			$arr_fl_rooms_ = array_shift($arr_fl_rooms);
			//$db->bind("frm",$frm_);
			if($arr_fl_rooms_ != 6) {
				$sql.=" AND (rooms='$arr_fl_rooms_'";
			 } else {
			 	$sql.=" AND (rooms >= '$arr_fl_rooms_'";
			 }
			if(count($arr_fl_rooms) >= 1) {
				 foreach($arr_fl_rooms as $fl_rooms_) {
					  //$db->bind("frm",$frm_);
					  if($fl_rooms_ != 6) {
					 	 $sql.=" OR rooms='$fl_rooms_'";
						} else {
							$sql.=" OR rooms >= '$fl_rooms_'";
						}
				  }
			  }
			 $sql.=")";
		  }
	 }
 
 if (isset($_GET['fl_phone']) && $_GET['fl_phone']!='') {
  $db->bind("fl_phone","%".$_GET['fl_phone']."%");
  $sql.=' AND phones LIKE :fl_phone';
 }

 if (isset($_GET['fl_date']) && $_GET['fl_date']!='') {
  $db->bind("fl_date", strtotime($_GET['fl_date']));
  $sql.=' AND adDate<=:fl_date';
 }
 
 if (isset($_GET['frm']) && is_array($_GET['frm'])) {
	 $arr_frm = $_GET['frm'];
		 if(count($arr_frm) >= 1) {
			$frm_ = array_shift($arr_frm);
			//$db->bind("frm",$frm_);
			$sql.=" AND (frm='$frm_'";
			if(count($arr_frm) >= 1) {
				 foreach($arr_frm as $frm_) {
					  //$db->bind("frm",$frm_);
					  $sql.=" OR frm='$frm_'";
				  }
			  }
			 $sql.=")";
		  }
	 }

  $page=0;
  if (isset($_GET['page'])) $page=$_GET['page'];
  $page*=50;
  $tp=$page+50;
 $offers = $db->query($sql." ORDER BY adDate DESC LIMIT $page,$tp");
 $ajax=false;
 if (isset($_GET['ajax'])) $ajax=true;

 if (isset($_GET['export'])) export($offers);

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
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"><!-- Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css"><!-- Bootstrap -->
	 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	 <link rel="stylesheet" href="css/chosen.min.css">
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="js/i18datepicker.js"></script>
		<script src="js/chosen.jquery.min.js"></script>

		<link rel="stylesheet" href="fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
		
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
    .table-soft {
    	font-size: 12px;
    	box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }
    /* centered columns styles */
.row-centered {
    text-align:center;

}

.col-centered {
    display:inline-block;
    float:none;
    /* reset the text-align */
    text-align:left;
    /* inline-block space fix */
    margin-right:-4px;
}
.pn{
	word-wrap: break-word;
	width: 100px;
}

.margin5 {
	margin: 5px 0px;
	}
.float {
	float:left;
	}
</style>
  </head>
  <body>

  <div class="container">
  	<div class="row-centered">
  		<div class="col-centered">
  	
  	<!--<img src="http://avangards.com.ua/sites/all/themes/avangards03a/images/object2007797612.png">-->
	<a href="/"><img src="images/logo.jpg" height="80px"></a>
  	</div>
  </div>
  <div class="row">

   <div style="padding-top:30px;">
 
			   <ul class="nav nav-pills pull-left">
				  <li <?php if ($op==1) echo 'class="active"' ?>><a href="?op=1">Продам</a></li>
				  <li <?php if ($op==2) echo 'class="active"' ?>><a href="?op=2">Сдам</a></li>
				  <li <?php if ($op==3) echo 'class="active"' ?>><a href="?op=3">Сдам посуточно</a></li>
				  <li <?php if ($op==4) echo 'class="active"' ?>><a href="?op=4">Куплю</a></li>
				  <li <?php if ($op==5) echo 'class="active"' ?>><a href="?op=5">Сниму</a></li>
				</ul>
				
			<div class="pull-right">
					<input class="form-control" type="submit" onClick="export_exel()" value="Експорт" style="margin-top:3px"></div>
			 </div>
			  <div class="clearfix bg-info"></div><br>
		<div class="form-div" style="overflow:hidden; height: 100px">
		   <form method="get" id="frm_filter" class="form-inline">
		   		 
					
				 		 
		   	  <fieldset>
			  <legend>Фильтр <input type="checkbox" id="show_hide_f" title="Раскрыть"  checked="checked">
			   <div class="pull-right bg-warning" style="font-size:12px">
								<label></label>
								<input type="checkbox" name="frm[0]" value="1" <?php if((isset($_GET['frm'][0]) && $_GET['frm'][0] == 1) || !isset($_GET['frm'])) {echo 'checked="checked"';} ?> class="frm_ch" /> Квадрат
							
								<input type="checkbox" name="frm[1]" value="2" <?php if((isset($_GET['frm'][1]) && $_GET['frm'][1] == 2) || !isset($_GET['frm'])) {echo 'checked="checked"';} ?> class="frm_ch"  /> Ray-2
							
								<input type="checkbox" name="frm[2]" value="3" <?php if((isset($_GET['frm'][2]) && $_GET['frm'][2] == 3) || !isset($_GET['frm'])) {echo 'checked="checked"';} ?> class="frm_ch" /> Сологуб

					 </div>
			  </legend>
				<div id="fieldset_form">
					 <input type="hidden" name="op" value="<?php echo $op; ?>" />
					 
			
					
					
					<div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="ro_d">
						Кол-во комнат <br>
						
									<input type="checkbox" class="" name="fl_rooms[1]" value="1" <?php if((isset($_GET['fl_rooms'][1]) && $_GET['fl_rooms'][1] == 1) || !isset($_GET['fl_rooms'])) {echo 'checked="checked"';} ?> /> 1 <br>
									<input type="checkbox" name="fl_rooms[2]" value="2" <?php if((isset($_GET['fl_rooms'][2]) && $_GET['fl_rooms'][2] == 2) || !isset($_GET['fl_rooms'])) {echo 'checked="checked"';} ?> /> 2 <br>
									<input type="checkbox" name="fl_rooms[3]" value="3" <?php if((isset($_GET['fl_rooms'][3]) && $_GET['fl_rooms'][3] == 3) || !isset($_GET['fl_rooms'])) {echo 'checked="checked"';} ?> /> 3 <br>
									<input type="checkbox" name="fl_rooms[4]" value="4" <?php if((isset($_GET['fl_rooms'][4]) && $_GET['fl_rooms'][4] == 4) || !isset($_GET['fl_rooms'])) {echo 'checked="checked"';} ?> /> 4 <br>
									<input type="checkbox" name="fl_rooms[5]" value="5" <?php if((isset($_GET['fl_rooms'][5]) && $_GET['fl_rooms'][5] == 5) || !isset($_GET['fl_rooms'])) {echo 'checked="checked"';} ?> /> 5 <br>
									<input type="checkbox" name="fl_rooms[6]" value="6" <?php if((isset($_GET['fl_rooms'][6]) && $_GET['fl_rooms'][6] == 6) || !isset($_GET['fl_rooms'])) {echo 'checked="checked"';} ?> /> больше 5

						</div>
					
					<div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="ar_d">
						Площадь от
						<input name="area_from" class="form-control input-sm" type="text" placeholder="от" value="<?php if (isset($_GET['area_from'])) echo($_GET['area_from']); ?>" />
							до	
							<input name="area_to" class="form-control input-sm" type="text" placeholder="до" value="<?php if (isset($_GET['area_to'])) echo($_GET['area_to']); ?>" />
					</div>	
				
					<div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="fl_d">
						Этаж от
						<input name="fl_floor_from" class="form-control input-sm" type="text" placeholder="от" value="<?php if (isset($_GET['fl_floor_from'])) echo($_GET['fl_floor_from']); ?>" />
							до	
							<input name="fl_floor_to" class="form-control input-sm" type="text" placeholder="до" value="<?php if (isset($_GET['fl_floor_to'])) echo($_GET['fl_floor_to']); ?>" />
					</div>
					
						<div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="pr_d">
							Цена от
							<input name="fl_price_from" class="form-control input-sm" type="text" placeholder="мин" value="<?php if (isset($_GET['fl_price_from'])) echo($_GET['fl_price_from']); ?>" />
							Цена до
							<input name="fl_price_to" class="form-control input-sm" type="text" placeholder="макс" value="<?php if (isset($_GET['fl_price_to'])) echo($_GET['fl_price_to']); ?>" />
						</div>
						
					<div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="ph_d">
						Укажите цифры телефона
						<input name="fl_phone" class="form-control" type="text" placeholder="Телефон" value="<?php if (isset($_GET['fl_phone'])) echo($_GET['fl_phone']); ?>" />
					</div>
					<div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="date_d">
					Выберите дату
						<input name="fl_date" class="form-control input-sm" type="text" placeholder="Нажмите, чтобы выбрать" id="datepicker" value="<?php if (isset($_GET['fl_date'])) echo($_GET['fl_date']); ?>" />
					</div>
					
					<div class="row-centered">
  						<div class="col-centered" style="margin-top:0px;">	
								<input class="btn btn-default" type="submit" value="Искать">
								<input class="btn btn-primary reset-filter" type="button" value="Обнулить фильтр">
						</div>
					</div>
				</div>
			</fieldset>

			 </form>
		</div>
    <table class="table table-striped table-bordered table-soft" style="margin-top:10px;">
      <thead>
        <tr>
            <th id="di_th" class="fil" data-attr="di_d">Микрорайон</th>
            <th id="st_th" class="fil" data-attr="st_d">Улица</th>
            <th id="ty_th" class="fil" data-attr="ty_d">Тип</th>
            <th id="ar_th" class="fil" data-attr="ar_d">Площадь</th> 
            <th id="fl_th" class="fil" data-attr="fl_d">Этаж</th>
            <th id="ro_th" class="fil" data-attr="ro_d">Комн</th>
            <th id="pr_th" class="fil" data-attr="pr_d">Цена</th>
            <th id="ph_th" class="fil" data-attr="ph_d">Телефон</th>
            <th id="date_th" class="fil" data-attr="date_d">Дата</th>
            <th>Дополнительно</th>
        </tr>
      </thead>
      <tbody id="sellsbody">
      <?php } foreach ($offers as $offer) {
      	# code...
       ?>
      <tr>
            <!-- <td><?php echo $offer['region'] ?></td> -->
            
            <td><?php echo $offer['district']; ?></td>
            <td><?php echo $offer['street']; ?></td>
            <td><?php echo $offer['estateType']; ?></td>
			 <td><?php echo $offer['area_json']; ?></td>
			<td><?php echo $offer['floor']; ?> / <?php echo $offer['max_floor']; ?></td>
            <td><?php echo $offer['rooms']; ?></td>
            <td><?php if ($offer['price']) echo $offer['price'].(($offer['operation']==1||$offer['operation']==4)?'$':' грн.'); else echo '---'; ?></td>
            <td><p class="pn"><?php echo $offer['phones']; ?></p></td>
            <td><?php echo date("d/m/Y H:i:s",$offer['adDate']); ?></td>
            <td><?php if ($offer['flags']==6 || $offer['flags']==8) echo "<span>R</span>"; ?>
            <?php if (!preg_match('/kg-r/', $offer['url'])&&$offer['url']) {?><a href="<?php echo $offer['url']; ?>" target="_blank">Источник</a><?php } ?>
            <a href="#" onClick="showmore(<?php echo $offer['id_mn']; ?>);return false;">Описание</a>
            <div style="display:none;" id='t<?php echo $offer['id_mn']; ?>' data-imageid="<?=($offer['images']);?>">
                <h4 style="display:none;"><?php echo typeop($offer['operation']); ?></h4>
	            <i class="pull-right" style="color: grey;">База:<?php if ($offer['frm']==2) echo("ray2"); elseif ($offer['frm']==1) echo("квадрат"); elseif ($offer['frm']==3) echo("сологуб"); ?></i>
	            <table class="table table-striped table-bordered">
	            	<tr><td><b>Цена:</b></td><td><?php if ($offer['price']) echo $offer['price'].(($offer['operation']==1||$offer['operation']==4)?'$':' грн.'); else echo '---'; ?></td></tr>
	            	<tr><td><b>Адрес:</b></td><td><?php echo $offer['street']; ?></td></tr>
	            	<tr><td><b>Комнат:</b></td><td><?php echo $offer['rooms']; ?></td></tr>
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

        <?php } if (!$ajax) { ?>
        

      </tbody>
      </table>
	  
	       
     <input class="form-control" id="loadmorebtn" type="submit" onClick="more()" value="Загрузить еще...">
	 
	 <div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="di_d">
	 				Выберите 1 или больше районов из списка<br>
						<select name="fl_mic[]" id="fl_mic" multiple="multiple">
							<option value="Выберите район">Выберите район</option>
							  <?php
							  	foreach($district as $dis) :
								if (isset($_GET['fl_mic']) && in_array($dis,$_GET['fl_mic'])) { 
									echo '<option value="'.$dis.'" selected="selected">'.$dis.'</option>';
								} else {
									echo '<option value="'.$dis.'">'.$dis.'</option>';
								}
								endforeach;
								  ?>	  
						</select>
					</div>
		
	<div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="st_d">
						Укажите часть названия улицы
						<input name="fl_street" class="form-control input-sm" type="text" placeholder="Улица" value="<?php if (isset($_GET['fl_street'])) echo($_GET['fl_street']); ?>" />
						
					</div>
					<div style="padding 5px; margin: 0px; height: 0px; visibility: hidden" id="ty_d">
						Выберите 1 или больше типов <br> недвижимости из списка<br>
						<?php //if (isset($_GET['fl_type'])) echo($_GET['fl_type']); ?>
						<select name="fl_type[]" id="fl_type" multiple="multiple" style="width: 200px" >
							  <option <?php if(isset($arr_fl_type1) && is_array($arr_fl_type1) && empty($arr_fl_type1)) { echo "selected"; } ?> disabled></option>
							  <option <?php if(isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Комната',$arr_fl_type1)) { echo "selected"; } ?> value="Комната">Комната</option>
							  <option <?php if(isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Квартира в новострое',$arr_fl_type1)) { echo "selected"; } ?> value="Квартира в новострое">Квартира в новострое</option>
							  <option <?php if(isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Квартира',$arr_fl_type1)) { echo "selected"; } ?> value="Квартира">Квартира</option>
							  <option <?php if(isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Часть дома',$arr_fl_type1)) { echo "selected"; } ?> value="Часть дома">Часть дома</option>
							  <option <?php if(isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Дом',$arr_fl_type1)) { echo "selected"; } ?> value="Дом">Дом</option>
							  <option <?php if(isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Комната с подсе',$arr_fl_type1)) { echo "selected"; } ?> value="Комната с подсе">Комната с подсе</option>
							  <option <?php if(isset($arr_fl_type1) && is_array($arr_fl_type1) && in_array('Квартира-суточн',$arr_fl_type1)) { echo "selected"; } ?> value="Квартира-суточн">Квартира-суточн</option>
						  </select>
					</div>
					
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <!-- <h5>Заголовок</h5> -->
                <p id="ofdesc" data-id="0">"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"</p>
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
<div id="linker"></div>
<div id="universal" style="position:absolute;background: rgba(222,222,222,.9);border: 1px solid #cccccc;height:auto;width:auto;">
	
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.reset-filter').click(function() {
			location.replace('/');
		});
		$("#fl_type,#fl_mic").chosen();
			$("#ty_d,#di_d").css({
						'height': '0px',
						'visibility': 'hidden'
					});
		var initial = $("#ty_d,#di_d,#st_d").detach();
		$('#frm_filter').append(initial);

		var height_th = $('.table-striped th:first').height()+(2*parseInt($('.table-striped th:first').css('padding-top')));
		var filter = [{"fl_rooms": '5'}]; //alert(filter[0].fl_rooms);
		$('.frm_ch').on('click', function() {
			$('#frm_filter').submit();			
		});
		

		$('#frm_filter').mouseover(function() {
			var active_value = $('#universal > div').detach();
			$('#frm_filter').append(active_value);
			$(active_value).css({
						'height': '0px',
						'visibility': 'hidden'
					});
			$('#universal').hide();
		});	
		
	
		$('#di_th, #date_th, #fl_th, #ro_th, #st_th, #ty_th, #ar_th, #pr_th, #ph_th').on('click', function() {
		
			var active_value = $('#universal > div').detach();
					$(active_value).css({
						'height': '0px',
						'visibility': 'hidden',
						'margin': '0px'
					});
			$('#frm_filter').append(active_value);

			var id = $(this).attr('data-attr');
			
			var html = $('#'+id).show().css({
							'height': 'auto',
							'visibility': 'visible',
							'margin': '5px'
							}).detach();
			$('#universal').html(html);
		});
		
		$('.fil').on('click', function() {
		var id = $(this).attr('id');
		var offset = $('#'+id).offset();
		var height = 1.5*parseInt($('#'+id).css('height'));
		var width = 1.5*parseInt($('#'+id).css('width'));
			$('#universal').css({
					'top': (offset.top+height_th+2)+'px',
					'left': offset.left+'px',
					'min-width': width+'px',
					'min-height': height+'px',
					}).html();
		$('#universal').show();	

		});
		
		$('#show_hide_f').on("click",function() {
			if($(this).is(':checked') == true) {
				$('#fieldset_form').show();
			} else {
				$('#fieldset_form').hide();
			}
		});
		
			
			
			 $( "#datepicker" ).datepicker({
				  changeMonth: true,
				  changeYear: true,
				});
				
			$( "#datepicker" ).datepicker( "option",
				$.datepicker.regional[ "ru" ] );
			
		 
	});
	
	function showmore(id){
		//$('#t'+id).show();

		
		$('#ofdesc').data('id',id).attr('data-id',id);
		$('#ofdesc').html($('#t'+id).html());
		$('#myModalLabel').html($('#t'+id+' h4').eq(0).html());
		$('#photos_div').html('');
				var images = $('#t'+id).attr('data-imageid');
				var html = '<div id="photos_div">';
		if(images != '') {
			var im_arr = images.split(':::');
			for(var i=0; i<im_arr.length;i++) {
				var linkp = '<a class="fancybox" rel="group" href="'+im_arr[i]+'"><img src="'+im_arr[i]+'" height="100px" style="border:1px solid #fff; margin: 1px;"></a>';
					html += linkp;
				}
		}
		
		html += '</div>'; 
		$('#ofdesc').after(html);
		$(".fancybox").fancybox();
		
		$('#myModal').modal('show');

		var cmn=$('#t'+id+' .comments');
		if (cmn.css('display')=='none')
		$.get( "comments.php",{id:id}).done( function( data ) {
		  // alert( "Data Loaded: " + data );
		  for (i in data) {
		  	cmn.append("<p style=\"color:grey;\">"+data[i].author+' '+data[i].date+"</p>")
		  	cmn.append("<p>"+data[i].message+"</p>")
		  }
		  if (data.length) {
			  cmn.show();
			  $('#ofdesc').html($('#t'+id).html());
		  }
		  // console.log(data);
		});
	}
	function addmessage(){
		var id=$('#ofdesc').data('id');
		var message=$('#txtcomment').val();
		// console.log(message);
		$.get("comments.php",{id:id,message:message}).done(function( data ){
			console.log('DONE!!!');
			$('#txtcomment').val('');
			var cmn=$('#t'+id+' .comments');
			cmn.append("<p style=\"color:grey;\">Вы только что</p>")
			cmn.append("<p>"+message+"</p>");
			cmn.show();
			$('#ofdesc').html($('#t'+id).html());
		});
	}
 var page=0;
  function more(){
    if (page<0) return;
    page++;
    $.get('index.php?'+($('#frm_filter').serialize())+'&ajax=true&page='+page,function(done){if (done!='') $('#sellsbody').append(done); else {page=-1;$('#loadmorebtn').hide();} });
  }
  function export_exel(){
    location.href='index.php?'+($('#frm_filter').serialize())+'&export=true';
  }
</script>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  </body>
</html>
<?php } ?>