<?php

require("classes.php");

function parse_file($file_name){

	$lastkey=0;
	$lastkey2=0;

	$done=[];
	$room_reg='/\s(\d)\D{0,1}\d{0,1}-(\D)/u';
	$fp = fopen($file_name, 'r');
		if ($fp) {
			while (!feof($fp)) {
				$mytext = fgets($fp, 999);
				$mytext = mb_convert_encoding($mytext, "UTF-8", "auto");
				if (preg_match('/^\s+(\d\d\d\d\d\d)\s+(\d+\.\d+\.\d+)/',$mytext,$match,PREG_OFFSET_CAPTURE)) {
					// echo $mytext.PHP_EOL;
					// var_dump($match);
					$id=$match[1][0];
					$date=$match[2][0];
					$offset=$match[2][1]+strlen($date);
					$analyse=substr($mytext,$offset);
					$find_price=substr($analyse,0,20);
						if (preg_match('/^\s+(\d{3,})\s/',$find_price,$match2))
							$price=$match2[1];
						else
							$price=0;

					$room_find = substr($analyse,0,30);
					$room_find = mb_convert_encoding($room_find, "UTF-8");

						if (preg_match($room_reg,$room_find,$match3))
							$room=$match3[1];
						else
							$room=null;

					$obl_find = mb_substr($mytext,69,20,"UTF-8");

					// var_dump("find:".$obl_find);

					$street=$phone=$descr='';

					if (preg_match('/\s(\w{4,})/u',$obl_find,$match4,PREG_OFFSET_CAPTURE))
					{
						// var_dump($match4);
						$obl = mb_substr($mytext, 69+$match4[1][1],16,"UTF-8");
						$lastkey=69+$match4[1][1];
						
						// var_dump($obl);
						if (preg_match('/(.*?)(\d{9})/u',mb_substr($mytext,69+$match4[1][1]+16,40,"UTF-8"),$match5))
						{
							// $street=mb_substr($mytext, 69+$match4[1][1]+16,$match5[1][1],"UTF-8");
							// var_dump($match5);
							$street=$match5[1];
							$phone=$match5[2];
							$descr=mb_substr($mytext,mb_strpos($mytext, $phone)+9);
							// var_dump("street:".$street);
							// var_dump("phone:".$phone);
							// var_dump("descr:".$descr);
							$lastkey2=mb_strpos($mytext, $phone);
						}
					}
					else 
						$obl='';

					// var_dump($id);
					// var_dump($date);
					// var_dump($price);
					// var_dump($room);
					// var_dump($analyse);

					$done[]=array(
						'id'=>$id,
						'date'=>$date,
						'price'=>$price,
						'room'=>$room,
						'obl'=>$obl,
						'street'=>$street,
						'phone'=>$phone,
						'descr'=>$descr,
						'an'=>$room_find,
						);
					
				} else 
					if (preg_match('/^                          /',$mytext)&&count($done))
					{
						$room_find=substr($mytext,30,30)." ";
						$room_find = mb_convert_encoding($room_find, "UTF-8");
						if (preg_match($room_reg,$room_find,$match3)) {
							// var_dump($match3);
							$done[count($done)-1]['room']=$match3[1];
						} //else echo("no match ".$find.PHP_EOL);
						$done[count($done)-1]['an'].="||".$room_find;
						if ($lastkey!=0) {
							$obl = mb_substr($mytext, $lastkey,16,"UTF-8");
							$done[count($done)-1]['obl'].=' '.$obl;
							$done[count($done)-1]['phone'].=" ".mb_substr($mytext,$lastkey2,9);
							$done[count($done)-1]['street'].=" ".mb_substr($mytext,$lastkey+16,$lastkey2-$lastkey-16);
							$done[count($done)-1]['descr'].=" ".mb_substr($mytext,$lastkey2+9);

						}
					} else $lastkey2=$lastkey=0;

			}
		} else echo "Ошибка при открытии файла";

	fclose($fp);

	foreach ($done as $key => $value) {
		// if ($value['room']===null) {
		// 	var_dump($value);
		// 	var_dump(preg_match($room_reg,$value['an'],$match3));
		// 	var_dump($match3);
		// }
		// var_dump($value['id']);
		// var_dump($value['phone']);
		$done[$key]['street']=preg_replace('/\s{2,}/',' ',$value['street']);
		$done[$key]['street']=preg_replace('/^\s/','',$value['street']);
		$done[$key]['street']=preg_replace('/(\r\n|\n)/','',$value['street']);
		$done[$key]['street']=preg_replace('/\s$/','',$value['street']);
		$done[$key]['obl']=preg_replace('/\s{2,}/',' ',$value['obl']);
		$done[$key]['obl']=preg_replace('/^\s/','',$value['obl']);
		$done[$key]['obl']=preg_replace('/(\r\n|\n)/','',$value['obl']);
		$done[$key]['obl']=preg_replace('/\s$/','',$value['obl']);
		// var_dump($value['obl']);
		// var_dump($value['street']);
		// $rez=$value['id']." - ".$value['price']." ".$value['room']." ".$value['obl']." ".$value['phone'];
		// var_dump($rez);
	}
	return $done;
}

function pyfile($id){
	$py=file_get_contents(dirname(__FILE__)."/pdf/pynp$id.txt");
		$py = explode("\n", $py);
		// var_dump($py);
		$npu=[];
		$max=0;
		$min=999;
		foreach ($py as $one) {
			if (preg_match('/(\d+) (\d+)/', $one,$match1)){
				$lst=count($npu);
				$max=max($max,$match1[1]);
				$min=min($min,$match1[1]);
				// var_dump($npu[$lst-1]['y']);
				// var_dump($match1[2]);
				if (!$lst || (0+$match1[2]-$npu[$lst-1]['y'])>10 )
					$npu[]=array('y'=>$match1[2],'x'=>$match1[1]);
			}
		}
		// var_dump($npu);
		$rzf=[];
		$rn=($max+$min)/2;
		foreach ($npu as $one) {
			$rzf[]=(($one['x']>$rn)?1:0);
		}
		// var_dump($rzf);
		return $rzf;
}


$dir    = dirname(__FILE__).'/pdf/';
$files = preg_grep('/^np\d\.pdf$/',scandir($dir));

var_dump($dir);
var_dump($files);
// 'test.xps.txt'

$total=[];

foreach($files as $file){
	if (preg_match('/^np(\d)/', $file,$match))
	{
		$id=$match[1];
		$arr=parse_file(dirname(__FILE__)."/pdf/np$id.txt");
		$flags=pyfile($id);
		if (count($arr)!=count($flags)){ echo("ERROR!"); exit();}
		foreach ($arr as $key=>$one) {
			$total[]=array_merge($one,array('type'=>$flags[$key]));
		}
	}
}
// var_dump($total);

$prs=new Parser(false);

foreach($total as $one){
	if ($one['room']==null) $one['room']=0;
	if (preg_match('/^(Днепропетровск|Днепродзержинск),?\s*(.*?)$/u', $one['obl'],$match)) {
		$city=$match[1];
		$district=$match[2];
	} else {
		$city='---';
		$district=$one['obl'];
		continue;
	}
	var_dump($one['id']);
	var_dump("city:".$city);
	// var_dump($district);
	$phones=array();
	if (preg_match_all('/(\d{9})/', $one['phone'],$matches))
		foreach ($matches as $key=>$match) {
			if (!$key) continue;
			// var_dump($match);
			$phones[]='0'.$match[0];
		}
	// var_dump($phones);
	$prs->sql_put_safe($one['id'],$one['date'],$one['price'],$one['room'],(($one['type']==1)?2:1),$city,$district,$one['street'],$one['descr'],$phones);

	
}

system('/bin/bash ./pdf/cleaner.sh');