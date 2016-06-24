<?php

set_time_limit(0);
require("classes.php");
$db = new Db();

			
				/*$sql="SELECT COUNT(*) as count FROM parsed WHERE is_revised=0";
				$result = $db->query($sql);
				//$pages = ceil($result[0]['count'] / 1000);
				$pages = 10;
				$query = "SELECT * FROM parsed WHERE is_revised = 0 ORDER BY adDate DESC LIMIT 1000";	
				
				for($i=1;$i<=$pages;$i++) {
					$result = $db->query($query);
						foreach($result as $val) :
							$rooms = $val['rooms'];
							$operation = $val['operation'];
							$price = $val['price'];
							$phones = $val['phones'];
							$id = $val['id'];
							$id_mn = $val['id_mn'];
							$frm = $val['frm'];
							$street = addslashes($val['street']);
							$floor = $val['floor'];
							$area = $val['area'];
							
							$check_query = "SELECT COUNT(*) as count FROM parsed WHERE rooms='$rooms' AND operation='$operation' AND price='$price' AND phones='$phones' AND frm='$frm' AND street='$street' AND floor = '$floor' AND area = '$area'";
							$check_result = $db->query($check_query);
							
							if($check_result[0]['count'] > 1) {
								//echo $id_mn."<br>";exit;
								$region = addslashes($val['region']);
								$city = addslashes($val['city']);
								$adistrict = addslashes($val['adistrict']);
								$district = addslashes($val['district']);
								$max_floor = $val['max_floor'];
								$area_json = $val['area_json'];
								$marketSegment = $val['marketSegment'];
								$estateType = addslashes($val['estateType']);
								$adDate = $val['adDate'];
								$text = addslashes($val['text']);
								$url = addslashes($val['url']);
								$images = addslashes($val['images']);
								$flags = $val['flags'];
								$time_added = $val['time_added'];
								$time_revised = date("Y-m-d H:i:s");
								$is_revised = 1;
								
								$remove_query = "DELETE FROM parsed WHERE rooms='$rooms' AND operation='$operation' AND price='$price' AND phones='$phones' AND frm='$frm' AND street='$street' AND floor = '$floor' AND area = '$area'";
								$db->query($remove_query);
								$insert_query = "INSERT INTO parsed (id_mn,id,frm,region,city,adistrict,district,street,rooms,floor,max_floor,area,area_json,operation,marketSegment,estateType,adDate,phones,price,text,url,images,flags,time_added,time_revised,is_revised) VALUES('$id_mn','$id','$frm','$region','$city','$adistrict','$district','$street','$rooms','$floor','$max_floor','$area','$area_json','$operation','$marketSegment','$estateType','$adDate','$phones','$price','$text','$url','$images','$flags','$time_added','$time_revised','$is_revised') ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)";
								$db->query($insert_query);
							} else {
								$update_query = "UPDATE parsed SET `is_revised`=1 WHERE id_mn='$id_mn'";
								$db->query($update_query);
							}
							//echo $id."<br>";
						endforeach; 
					
				}*/

                       
			$sql="SELECT COUNT(*) as count FROM parsed WHERE is_revised=0";
				$result = $db->query($sql);
				//$pages = ceil($result[0]['count'] / 1000);
				$pages = 10;
				$query = "SELECT * FROM parsed WHERE is_revised = 0 ORDER BY adDate DESC LIMIT 1000";	
				
				for($i=1;$i<=$pages;$i++) {
					$result = $db->query($query);
						foreach($result as $val) :
							$area_json = !empty($val['area_json']) ? $val['area_json'] : '';
							$id_mn = $val['id_mn'];
							$date = date('Y-m-d H:i:s');
							if(!empty($area_json)) {
								$area = explode('/',$area_json);
								$area = trim($area[0]);
							
								$insert_query = "UPDATE parsed SET `is_revised`=1,`time_revised`='$date',`area`='$area' WHERE id_mn='$id_mn'";
								$db->query($insert_query);
							} else {
							$update_query = "UPDATE parsed SET `is_revised`=1,`time_revised`='$date' WHERE id_mn='$id_mn'";
							$db->query($update_query);
							}
							//echo $id."<br>";
						endforeach; 
					
				}
			
			?>