<?php 
session_start();
set_time_limit(0);
require("classes.php");
$db = new Db();
$prs=new Parser(false);


$client=new SoapClient("http://ray-2.com/Services/FlatsService.svc?wsdl");
$time_client=new SoapClient("http://ray-2.com/Services/TimeService.svc?wsdl");
$client_images=new SoapClient("http://ray-2.com/Services/ImagesService.svc?wsdl");
$client3=new SoapClient("http://ray-2.com/Services/SecurityService.svc?wsdl");
$client_houses=new SoapClient("http://ray-2.com/Services/HousesService.svc?wsdl");

$auth = array('login'=>'zaderey','password'=>'1111');
unset($_SESSION['key']);
if(!isset($_SESSION['key'])) {

$options = $db->query("SELECT option_value FROM `options` WHERE `option_name` = 'ray2_last_datetime'");
$time = $time_client->__soapCall("GetCurrentTime", array());
$current_time = $time->GetCurrentTimeResult;
$from_date = $options[0]['option_value'];

setNewTime($db, $current_time);

$response3 = $client3->__soapCall("Login", array($auth));
		
if(isset($response3->LoginResult->Roles->Role->AgencyId)) {
	$agency = array('sessionId'=>$response3->LoginResult->SessionId,'agencyId'=>$response3->LoginResult->Roles->Role->AgencyId);
	// print_r($agency);
	$session = $response3->LoginResult->SessionId;
	$session_arr = array('sessionId'=>$session);
		$client3->__soapCall("SetAgency", array($agency));
		
	//	$auth1 = array('login'=>'zaderey','password'=>'65219711', 'entity'=>$response3->LoginResult->Roles->Role->AgencyId);
	// print_r($agency););

//$response3 = $client3->__soapCall("Logout", array($auth1));

//exit;
		$_SESSION['key'] = $session;
		//$vars_image = array('sessionId'=>$_SESSION['key'],'flatId'=>'755945');
		//$images = $client_images->__soapCall("GetFlatImages", array($vars_image));
		//print_r($images->GetFlatImagesResult->Image[0]->Url);
		//$points = new SoapClient("http://ray-2.com/Services/DictionariesService.svc?wsdl");

	//$countries_array = $locations->__soapCall("GetCountries", array($session_arr));
		//$p = $points->__soapCall("GetDictionaries", array($session_arr));
		//$client11 =new SoapClient("http://ray-2.com/Services/FlatRequestsService.svc?wsdl");
	//	$vars = array('sessionId'=>$_SESSION['key'], 'fromDate'=>$from_date);
	//	$flats = $client_houses->__soapCall("GetByDateRange", array($vars));
	//	print_r($flats);
		//print_r($p);
	//	exit;
		$loc_arrays = getLocationsArrays($session_arr);	
		$vars = array('sessionId'=>$_SESSION['key'], 'fromDate'=>$from_date);
		$vars1 = array('sessionId'=>$_SESSION['key'], 'flatid'=>'755945');
		$flats = $client->__soapCall("GetByDateRange", array($vars));
		$houses = $client_houses->__soapCall("GetByDateRange", array($vars));
		foreach($houses->GetByDateRangeResult->House as $v) {

			$time_c = explode('.', str_replace('T', ' ', $v->CONT_DATE));
			$date_cp = date('Y-m-d H:i:s', (strtotime($time_c[0])+10800));
			
			$images_house = array();
			$photos_json = '';
			if(isset($v->ImagesCount) && $v->ImagesCount > 0) {
				$options = array(
						CURLOPT_RETURNTRANSFER => TRUE,
						);
				$url = $v->Url;
				//$url = 'http://dnepropetrovsk.dnp.olx.ua/obyavlenie/prodam-1komn-na-pravde-kirpichnyy-dom-IDfrQVU.html';
							$ch = curl_init($url);
							curl_setopt_array($ch, $options);		
							$result = curl_exec($ch);
				$preg_image = '/<div class="photo-handler rel inlblk">.*<img src="([a-zA-Z0-9\s\'\"=_\-:;%,\.{}\/]*)".*>/Usi';
				$preg_image1 = '/<div class="tcenter img-item">.*<div class="photo-glow">.*<img src="([a-zA-Z0-9\s\'\"=_\-:;%,\.{}\/]*)".*>/Usi';
				preg_match_all($preg_image,$result,$match);
				preg_match_all($preg_image1,$result,$match1);
				//print_r($match[1]);print_r($match1[1]);
				
				if(isset($match[1])) {
					$images_house = array_merge($images_house,$match[1]);
					}
				
				if(isset($match1[1])) {
					$images_house = array_merge($images_house,$match1[1]);
					}
					
				$photos_json = !empty($images_house) ? addslashes(implode(':::',$images_house)) : '';			
			}
			
			if(isset($v->PriceRent))  { 
			 	 $price = $v->PriceRent;
			} else if(isset($v->CENA)) { 
				$price = $v->CENA; 
			} else { 
				$price = '';
			} 
			
			if(isset($v->ForRent)) {
				$operat = 2;
				} else if(isset($v->ForSale)) {
				$operat = 1;
				}
			$max_floor = isset($v->ETAZHEY) ? $v->ETAZHEY : '---'; 
			$floor = $max_floor;
			$rooms = isset($v->KOMNAT) ? $v->KOMNAT : 0;
			$city = isset($v->City) ? $loc_arrays['cities'][$v->City] : '---';
			$estate_type = 'Дом'; 
			$adistrict = isset($v->District) ? $loc_arrays['districts'][$v->District] : '---'; 
			$street = isset($v->Street) ? $loc_arrays['streets'][$v->Street] : '---';
			
			$phones = array(isset($v->KONTTEL1) ? $v->KONTTEL1 : '');
			if(isset($v->KONTTEL2)) { $phones[] = $v->KONTTEL2; }
			if(isset($v->KONTTEL3)) { $phones[] = $v->KONTTEL3; }
			$area = isset($v->SALL) ? $v->SALL : '---';
			$area_json = isset($v->SALL) ? $v->SALL.' / ' : ' / ';
			$area_json .= isset($v->SZHIL) ? $v->SZHIL.' / ' : '0 / '; 
			$area_json .= isset($v->SKITCHEN) ? $v->SKITCHEN : '0';
			$district = isset($v->Zone) ? $loc_arrays['zones'][$v->Zone] : '---'; //Район district
			$url = isset($v->Url) ? $v->Url : '';
			$desc = "Дом находится в городе $city, район $district ($adistrict). Ссылка $url";
		
			$prs->sql_ray2_put_safe('', $date_cp, $price, $rooms, $operat, $city, $estate_type, $floor, $max_floor, $district, $street, $desc, $phones, 0, $area, $area_json, $url, $adistrict,$photos_json);
			echo $date_cp;
		}	
		
		foreach($flats->GetByDateRangeResult->Flat as $v) {
		//print_r($v);
			$time_c = explode('.', str_replace('T', ' ', $v->CONT_DATE));
			$date_cp = date('Y-m-d H:i:s', (strtotime($time_c[0])+10800));
			
			//echo $date_cp;exit;
			$images_flat = array();
			$photos_json = '';
			if(isset($v->ImagesCount) && $v->ImagesCount > 0) {
				$options = array(
						CURLOPT_RETURNTRANSFER => TRUE,
						);
				$url = $v->Url;
				//$url = 'http://dnepropetrovsk.dnp.olx.ua/obyavlenie/prodam-1komn-na-pravde-kirpichnyy-dom-IDfrQVU.html';
							$ch = curl_init($url);
							curl_setopt_array($ch, $options);		
							$result = curl_exec($ch);
				$preg_image = '/<div class="photo-handler rel inlblk">.*<img src="([a-zA-Z0-9\s\'\"=_\-:;%,\.{}\/]*)".*>/Usi';
				$preg_image1 = '/<div class="tcenter img-item">.*<div class="photo-glow">.*<img src="([a-zA-Z0-9\s\'\"=_\-:;%,\.{}\/]*)".*>/Usi';
				preg_match_all($preg_image,$result,$match);
				preg_match_all($preg_image1,$result,$match1);
				//print_r($match[1]);print_r($match1[1]);
				
				if(isset($match[1])) {
					$images_flat = array_merge($images_flat,$match[1]);
					}
				
				if(isset($match1[1])) {
					$images_flat = array_merge($images_flat,$match1[1]);
					}
					
				$photos_json = !empty($images_flat) ? addslashes(implode(':::',$images_flat)) : '';			
			}
			
			if(isset($v->CenaRentMonth))  { 
			 	 $price = $v->CenaRentMonth;
				 $operat = 2;
			} else if(isset($v->CENA)) { 
				$price = $v->CENA; 
				$operat = 1;
			} else {
				$operat = 1;
				$price = 0; 
			}
			
			$floor = isset($v->ETAZH) ? $v->ETAZH : '0';
			$max_floor = isset($v->ETAZHEY) ? $v->ETAZHEY : '0'; 
			$rooms = isset($v->KOMNAT) ? $v->KOMNAT : 0;
			$city = isset($v->City) ? $loc_arrays['cities'][$v->City] : '---';
			$estate_type = 'Квартира'; 
			$adistrict = isset($v->District) ? $loc_arrays['districts'][$v->District] : '---'; 
			$street = isset($v->Street) ? $loc_arrays['streets'][$v->Street] : '---';
			
			$phones = array(isset($v->KONTTEL1) ? $v->KONTTEL1 : '');
			if(isset($v->KONTTEL2)) { $phones[] = $v->KONTTEL2; }
			if(isset($v->KONTTEL3)) { $phones[] = $v->KONTTEL3; }
			$area = isset($v->SALL) ? $v->SALL : '---';
			$area_json = isset($v->SALL) ? $v->SALL.' / ' : ' / ';
			$area_json .= isset($v->SZHIL) ? $v->SZHIL.' / ' : '0 / '; 
			$area_json .= isset($v->SKITCHEN) ? $v->SKITCHEN : '0';
			$district = isset($v->Zone) ? $loc_arrays['zones'][$v->Zone] : '---'; //Район district
			$url = isset($v->Url) ? $v->Url : '';
			$desc = "Квартира находится в городе $city, район $district ($adistrict). Ссылка $url";
		
			$prs->sql_ray2_put_safe('', $date_cp, $price, $rooms, $operat, $city, $estate_type, $floor, $max_floor, $district, $street, $desc, $phones, 0, $area, $area_json, $url, $adistrict,$photos_json);
			echo $date_cp;
		}	 
	}
}				

function getLocationsArrays($session_arr) {

$locations = new SoapClient("http://ray-2.com/Services/LocationsService.svc?wsdl");

	//$countries_array = $locations->__soapCall("GetCountries", array($session_arr));
	$regions_array = $locations->__soapCall("GetRegions", array($session_arr));
	$regions = array();
		foreach($regions_array->GetRegionsResult->Region as $val) :
			$regions[$val->Id] = $val->Name;
		endforeach;
	//$areas_array = $locations->__soapCall("GetAreas", array($session_arr));
	$districts_array = $locations->__soapCall("GetDistricts", array($session_arr));
	$districts = array();
		foreach($districts_array->GetDistrictsResult->District as $val) :
			$districts[$val->Id] = $val->Name;
		endforeach;
	$cities_array = $locations->__soapCall("GetCities", array($session_arr));
	$cities = array();
		foreach($cities_array->GetCitiesResult->City as $val) :
			$cities[$val->Id] = $val->Name;
		endforeach;
	$zones_array = $locations->__soapCall("GetZones", array($session_arr));
	$zones = array();
	foreach($zones_array->GetZonesResult->Zone as $val) :
			$zones[$val->Id] = $val->Name;
		endforeach;
	$streets_array = $locations->__soapCall("GetStreets", array($session_arr));
	$streets = array();
	foreach($streets_array->GetStreetsResult->Street as $val) :
			$streets[$val->Id] = $val->Name;
		endforeach;

	//print_r($regions);//-1 id to fit index

	return array(
			'regions' => $regions,
			'cities' => $cities,
			'districts' => $districts,
			'zones' => $zones,
			'streets' => $streets
			);
}

function setNewTime($db, $time) {
	$db->query("UPDATE`options` SET `option_value` = '$time' WHERE `option_name` = 'ray2_last_datetime'");
}





/* Flat {
 guid AgencyId;
 string AgencyName;
 int Area;
 int BALKON;
 int BRONDOOR;
 int BTI;
 int CABLETV;
 double CENA;
 dateTime CLOSE2DATE;
 dateTime CLOSE_DATE;
 int COKOL;
 int COMMERCIAL;
 int CONDITION;
 dateTime CONT_DATE;
 int COUNTG;
 int COUNTW;
 int CenaRentMonth;
 ObjectChangeTracker ChangeTracker;
 int City;
 int Class;
 int Country;
 int DOCUMENTS;
 int DistanceToSea;
 int District;
 int ELECTRO;
 string EMail;
 dateTime ENTER_DATE;
 int ETAZH;
 int ETAZHEY;
 int FINISH;
 int FOND;
 ArrayOfFlatAgentPhone FlatAgentPhones;
 ArrayOfFlatAssoc FlatAssocs;
 boolean ForRent;
 boolean ForSale;
 int Furniture;
 double H_POTOLKA;
 string House;
 int ISOKOMNAT;
 int Id;
 ArrayOfImage Images;
 int ImagesCount;
 boolean IsPublishedOnSite;
 int KAFEL;
 string KODPODJEZD;
 int KOLONKA;
 int KOMNAT;
 int KONSYERJ;
 string KONTLITCO;
 string KONTTEL1;
 string KONTTEL2;
 string KONTTEL3;
 int KORPUS;
 int KVARTIRA;
 int LIFT;
 int LODGIYA;
 int MATERIAL;
 boolean NoPosr;
 string Notes;
 int OKNA;
 int OKNADVOR;
 int OKNARED;
 int OKNA_TIP;
 string ORIENTIR;
 int OTDVHOD;
 int Orientirs;
 int PARKET;
 int PECHKA;
 int PEREK;
 int PEREPLAN;
 int PHONE;
 int POGREB;
 dateTime PREDL_DATE;
 int ParentId;
 int Parking;
 int Planirovka;
 int PriceSquareMeter;
 string PublisherTexts;
 int REDLINE;
 string REMARKS;
 string REMARKS1;
 double REQ_ID;
 int RESHETKI;
 int ROOF;
 int Refrigerator;
 int Region;
 guid RoleId;
 double SALL;
 int SANTEHNIKA;
 int SANUZEL;
 int SIGNAL;
 double SKITCHEN;
 int SOSTOYANIE;
 double SZHIL;
 string Source;
 int SourceType;
 int Status;
 int Street;
 int SubwayStation;
 int TAMBUR;
 int TIPDOMA;
 int TORG;
 int TRUBA;
 int TV;
 int TehEtazh;
 int UGLOVAYA;
 string Url;
 guid UserId;
 int VODA;
 base64Binary Version;
 int WashMachine;
 int Zone;
 ID Id;
 IDREF Ref;
 */
?>